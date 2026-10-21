<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinishBetaRequest;
use App\Http\Requests\ProgressBetaRequest;
use App\Http\Requests\StartBetaRequest;
use App\Mail\BetaConfirmation;
use App\Models\BetaLead;
use App\Support\Content;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BetaController extends Controller
{
    private const SESSION_KEY = 'beta_lead_id';

    public function __construct(private readonly Content $content) {}

    /** Krok 1 (gdy ktoś wejdzie na /beta bezpośrednio): samo pole e-mail. */
    public function show(Request $request): View|RedirectResponse
    {
        if ($this->currentLead($request)) {
            return redirect()->route('beta.survey');
        }

        return view('beta.start', ['site' => $this->content->site()]);
    }

    /** Zapis e-maila ze statusem „niedokończony” i przejście do ankiety. */
    public function start(StartBetaRequest $request): RedirectResponse
    {
        if ($request->isSpam()) {
            return redirect()->route('beta.thanks');
        }

        $email = BetaLead::normalizeEmail($request->string('email'));
        $lead = BetaLead::query()->where('email', $email)->first();

        if ($lead && $lead->isOnList()) {
            return back()->withInput()->withErrors(['email' => 'Ten adres jest już na liście.'], 'beta');
        }

        if (! $lead) {
            $lead = new BetaLead(['email' => $email, 'status' => BetaLead::STATUS_INCOMPLETE, 'last_step' => 1]);
        }

        // Niedokończony zapis: aktualizujemy źródło tylko, jeśli go jeszcze nie ma.
        foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'referrer'] as $field) {
            if (blank($lead->{$field}) && $request->filled($field)) {
                $lead->{$field} = $request->string($field)->limit($field === 'referrer' ? 500 : 200, '')->toString();
            }
        }
        // Bez JS: nagłówek Referer, ale tylko jeśli prowadzi spoza naszej domeny.
        $referer = (string) $request->headers->get('referer', '');
        if (blank($lead->referrer) && $referer !== '' && parse_url($referer, PHP_URL_HOST) !== $request->getHost()) {
            $lead->referrer = mb_substr($referer, 0, 500);
        }

        $lead->ensureTokens();
        $lead->save();

        $request->session()->put(self::SESSION_KEY, $lead->id);

        return redirect()->route('beta.survey');
    }

    /** Ankieta: pytania 2–5 + zgody. */
    public function survey(Request $request): View|RedirectResponse
    {
        $lead = $this->currentLead($request);
        if (! $lead) {
            return redirect()->route('beta.show');
        }

        return view('beta.survey', [
            'lead' => $lead,
            'site' => $this->content->site(),
            'form' => $this->content->form(),
            'integrations' => $this->content->integrations(),
            'maxIntegrations' => (int) config('beta.max_integrations'),
        ]);
    }

    /** Zapis postępu po każdym kroku (fetch z JS), żeby nie tracić porzuconych zapisów. */
    public function progress(ProgressBetaRequest $request): JsonResponse
    {
        $lead = $this->currentLead($request);
        if (! $lead) {
            return response()->json(['ok' => false, 'redirect' => route('beta.show')], 419);
        }

        $data = $request->validated();
        $lead->fill(array_filter([
            'business_type' => $data['business_type'] ?? null,
            'business_type_other' => ($data['business_type'] ?? null) === 'inne' ? ($data['business_type_other'] ?? null) : null,
            'integrations' => $data['integrations'] ?? null,
            'missing_text' => $data['missing_text'] ?? null,
            'spend_range' => $data['spend_range'] ?? null,
        ], fn ($v) => $v !== null));
        $lead->last_step = max((int) $lead->last_step, (int) $data['last_step']);
        $lead->save();

        return response()->json(['ok' => true]);
    }

    /** Ostatni krok: pełna walidacja, zgody, mail double opt-in. */
    public function finish(FinishBetaRequest $request): RedirectResponse
    {
        $lead = $this->currentLead($request);
        if (! $lead) {
            return redirect()->route('beta.show');
        }

        if ($request->filled('firma_www')) {
            $request->session()->forget(self::SESSION_KEY);

            return redirect()->route('beta.thanks');
        }

        $data = $request->validated();
        $consents = $this->content->form()['consents'];
        $now = now();

        $lead->fill([
            'business_type' => $data['business_type'],
            'business_type_other' => $data['business_type'] === 'inne' ? ($data['business_type_other'] ?? null) : null,
            'integrations' => array_values($data['integrations']),
            'missing_text' => $data['missing_text'] ?? null,
            'spend_range' => $data['spend_range'] ?? null,
            'consent_contact_at' => $now,
            'consent_contact_text' => $this->content->fill($consents['contact']),
            'consent_marketing_at' => ! empty($data['consent_marketing']) ? $now : null,
            'consent_marketing_text' => ! empty($data['consent_marketing']) ? $this->content->fill($consents['marketing']) : null,
            'status' => BetaLead::STATUS_COMPLETE,
            'completed_at' => $now,
            'last_step' => 5,
        ]);
        $lead->ensureTokens();
        $lead->save();

        Mail::to($lead->email)->send(new BetaConfirmation($lead));

        $request->session()->forget(self::SESSION_KEY);
        $request->session()->flash('beta_completed', true);

        return redirect()->route('beta.thanks');
    }

    public function thanks(): View
    {
        return view('beta.thanks', ['site' => $this->content->site()]);
    }

    public function confirm(string $token): View
    {
        $lead = BetaLead::query()->where('confirm_token', $token)->first();

        if ($lead && $lead->status === BetaLead::STATUS_COMPLETE) {
            $lead->forceFill(['status' => BetaLead::STATUS_CONFIRMED, 'confirmed_at' => now()])->save();
        }

        return view('beta.confirmed', [
            'site' => $this->content->site(),
            'valid' => $lead !== null && $lead->status === BetaLead::STATUS_CONFIRMED,
        ]);
    }

    public function unsubscribe(string $token): View
    {
        $lead = BetaLead::query()->where('unsubscribe_token', $token)->first();
        $lead?->anonymize();

        return view('beta.unsubscribed', ['site' => $this->content->site(), 'valid' => $lead !== null]);
    }

    private function currentLead(Request $request): ?BetaLead
    {
        $id = $request->session()->get(self::SESSION_KEY);
        if (! $id) {
            return null;
        }

        $lead = BetaLead::query()->find($id);
        if (! $lead || $lead->status !== BetaLead::STATUS_INCOMPLETE) {
            $request->session()->forget(self::SESSION_KEY);

            return null;
        }

        return $lead;
    }
}
