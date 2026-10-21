<?php

namespace Tests\Feature;

use App\Mail\BetaConfirmation;
use App\Models\BetaLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class BetaSignupTest extends TestCase
{
    use RefreshDatabase;

    private function startSignup(string $email = 'jan@example.pl', array $extra = []): TestResponse
    {
        return $this->post(route('beta.start'), ['email' => $email] + $extra);
    }

    private function validSurvey(array $overrides = []): array
    {
        return array_merge([
            'business_type' => 'sklep',
            'integrations' => ['allegro', 'przelewy24'],
            'missing_text' => 'Brakuje Sellasist',
            'spend_range' => 'do-500',
            'consent_contact' => '1',
        ], $overrides);
    }

    public function test_email_is_saved_immediately_as_incomplete(): void
    {
        $this->startSignup('Jan@Example.pl ', ['utm_source' => 'fb', 'utm_campaign' => 'start'])
            ->assertRedirect(route('beta.survey'));

        $this->assertDatabaseHas('beta_leads', [
            'email' => 'jan@example.pl',
            'status' => BetaLead::STATUS_INCOMPLETE,
            'utm_source' => 'fb',
            'utm_campaign' => 'start',
        ]);
    }

    public function test_signup_requires_email(): void
    {
        $this->from('/')->post(route('beta.start'), ['email' => ''])
            ->assertRedirect('/')
            ->assertSessionHasErrorsIn('beta', ['email']);
        $this->assertDatabaseCount('beta_leads', 0);

        $this->from('/')->post(route('beta.start'), ['email' => 'nie-email'])
            ->assertSessionHasErrorsIn('beta', ['email']);
    }

    public function test_honeypot_blocks_bots_silently(): void
    {
        $this->startSignup('bot@example.pl', ['firma_www' => 'http://spam'])
            ->assertRedirect(route('beta.thanks'));
        $this->assertDatabaseCount('beta_leads', 0);
    }

    public function test_survey_requires_lead_in_session(): void
    {
        $this->get(route('beta.survey'))->assertRedirect(route('beta.show'));
        $this->post(route('beta.finish'), $this->validSurvey())->assertRedirect(route('beta.show'));
    }

    public function test_full_signup_stores_everything_and_sends_double_opt_in(): void
    {
        Mail::fake();
        $this->startSignup('jan@example.pl', ['utm_medium' => 'cpc', 'referrer' => 'https://google.pl/']);

        $this->post(route('beta.finish'), $this->validSurvey(['consent_marketing' => '1']))
            ->assertRedirect(route('beta.thanks'));

        $lead = BetaLead::firstOrFail();
        $this->assertSame(BetaLead::STATUS_COMPLETE, $lead->status);
        $this->assertSame('sklep', $lead->business_type);
        $this->assertSame(['allegro', 'przelewy24'], $lead->integrations);
        $this->assertSame('Brakuje Sellasist', $lead->missing_text);
        $this->assertSame('do-500', $lead->spend_range);
        $this->assertSame('cpc', $lead->utm_medium);
        $this->assertSame('https://google.pl/', $lead->referrer);
        $this->assertNotNull($lead->consent_contact_at);
        $this->assertStringContainsString('zapisu do bety', $lead->consent_contact_text);
        $this->assertNotNull($lead->consent_marketing_at);
        $this->assertNotNull($lead->consent_marketing_text);
        $this->assertSame(5, $lead->last_step);
        $this->assertNotNull($lead->confirm_token);
        $this->assertNotNull($lead->unsubscribe_token);

        Mail::assertSent(BetaConfirmation::class, fn ($mail) => $mail->hasTo('jan@example.pl'));
    }

    public function test_marketing_consent_is_optional_and_off_by_default(): void
    {
        Mail::fake();
        $this->startSignup();
        $this->post(route('beta.finish'), $this->validSurvey())->assertRedirect(route('beta.thanks'));

        $lead = BetaLead::firstOrFail();
        $this->assertNull($lead->consent_marketing_at);
        $this->assertNull($lead->consent_marketing_text);
        $this->assertNotNull($lead->consent_contact_at);
    }

    public function test_cannot_finish_without_business_type_integration_or_contact_consent(): void
    {
        Mail::fake();
        $this->startSignup();

        $this->post(route('beta.finish'), $this->validSurvey(['business_type' => null]))
            ->assertSessionHasErrors(['business_type']);
        $this->post(route('beta.finish'), $this->validSurvey(['integrations' => []]))
            ->assertSessionHasErrors(['integrations']);
        $this->post(route('beta.finish'), $this->validSurvey(['consent_contact' => null]))
            ->assertSessionHasErrors(['consent_contact']);
        $this->post(route('beta.finish'), $this->validSurvey(['business_type' => 'inne', 'business_type_other' => '']))
            ->assertSessionHasErrors(['business_type_other']);

        $this->assertSame(BetaLead::STATUS_INCOMPLETE, BetaLead::firstOrFail()->status);
        Mail::assertNothingSent();
    }

    public function test_cannot_select_more_than_three_integrations_or_unknown_ids(): void
    {
        $this->startSignup();

        $this->post(route('beta.finish'), $this->validSurvey(['integrations' => ['allegro', 'blik', 'ksef', 'inpost']]))
            ->assertSessionHasErrors(['integrations']);
        $this->post(route('beta.finish'), $this->validSurvey(['integrations' => ['nie-istnieje']]))
            ->assertSessionHasErrors(['integrations.0']);
        $this->post(route('beta.finish'), $this->validSurvey(['integrations' => ['allegro', 'allegro']]))
            ->assertSessionHasErrors(['integrations.0']);

        $this->postJson(route('beta.progress'), ['last_step' => 3, 'integrations' => ['allegro', 'blik', 'ksef', 'inpost']])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['integrations']);
    }

    public function test_same_email_does_not_create_duplicate(): void
    {
        Mail::fake();
        $this->startSignup();
        $this->post(route('beta.finish'), $this->validSurvey());

        $this->from('/')->startSignup('JAN@example.pl')
            ->assertRedirect('/')
            ->assertSessionHasErrorsIn('beta', ['email']);
        $this->assertSame('Ten adres jest już na liście.', session('errors')->getBag('beta')->first('email'));
        $this->assertDatabaseCount('beta_leads', 1);
    }

    public function test_abandoned_signup_can_be_resumed_without_duplicate(): void
    {
        $this->startSignup();
        $this->postJson(route('beta.progress'), ['last_step' => 2, 'business_type' => 'b2b'])->assertOk();

        $lead = BetaLead::firstOrFail();
        $this->assertSame(2, $lead->last_step);
        $this->assertSame('b2b', $lead->business_type);

        // Drugi zapis tego samego, niedokończonego adresu: ten sam rekord.
        $this->flushSession();
        $this->startSignup()->assertRedirect(route('beta.survey'));
        $this->assertDatabaseCount('beta_leads', 1);
        $this->get(route('beta.survey'))->assertOk()->assertSee('jan@example.pl');
    }

    public function test_progress_keeps_highest_step(): void
    {
        $this->startSignup();
        $this->postJson(route('beta.progress'), ['last_step' => 3])->assertOk();
        $this->postJson(route('beta.progress'), ['last_step' => 2])->assertOk();
        $this->assertSame(3, BetaLead::firstOrFail()->last_step);
    }

    public function test_confirmation_link_marks_lead_confirmed(): void
    {
        Mail::fake();
        $this->startSignup();
        $this->post(route('beta.finish'), $this->validSurvey());
        $lead = BetaLead::firstOrFail();

        $this->get(route('beta.confirm', $lead->confirm_token))->assertOk()->assertSee('zapis potwierdzony');
        $this->assertSame(BetaLead::STATUS_CONFIRMED, $lead->fresh()->status);
        $this->assertNotNull($lead->fresh()->confirmed_at);

        $this->get(route('beta.confirm', 'zly-token'))->assertOk()->assertSee('nieaktywny');
    }

    public function test_confirmation_mail_contains_confirm_and_unsubscribe_links(): void
    {
        $lead = BetaLead::create(['email' => 'a@b.pl', 'status' => BetaLead::STATUS_COMPLETE]);
        $lead->ensureTokens();
        $lead->save();

        $mail = new BetaConfirmation($lead);
        $mail->assertSeeInHtml(route('beta.confirm', $lead->confirm_token));
        $mail->assertSeeInHtml(route('unsubscribe', $lead->unsubscribe_token));
        $mail->assertSeeInText(route('unsubscribe', $lead->unsubscribe_token));
        $mail->assertHasSubject('Potwierdź zapis do bety');
    }

    public function test_unsubscribe_anonymizes_lead(): void
    {
        Mail::fake();
        $this->startSignup();
        $this->post(route('beta.finish'), $this->validSurvey());
        $lead = BetaLead::firstOrFail();

        $this->get(route('unsubscribe', $lead->unsubscribe_token))->assertOk()->assertSee('Wypisaliśmy');
        $lead->refresh();
        $this->assertNull($lead->email);
        $this->assertNull($lead->missing_text);
        $this->assertSame(BetaLead::STATUS_UNSUBSCRIBED, $lead->status);
        $this->assertNotNull($lead->unsubscribed_at);

        // Po wypisaniu ten sam adres może zapisać się ponownie.
        $this->flushSession();
        $this->startSignup()->assertRedirect(route('beta.survey'));
        $this->assertDatabaseCount('beta_leads', 2);
    }

    public function test_rate_limit_on_signup(): void
    {
        config(['beta.rate_limit.start' => 2]);
        $this->startSignup('a@example.pl')->assertRedirect();
        $this->flushSession();
        $this->startSignup('b@example.pl')->assertRedirect();
        $this->flushSession();
        $this->startSignup('c@example.pl')->assertStatus(429);
    }
}
