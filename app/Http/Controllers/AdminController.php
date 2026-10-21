<?php

namespace App\Http\Controllers;

use App\Models\BetaLead;
use App\Support\Content;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Panel wewnętrzny: liczby, ranking integracji, podział na typy firm, eksport CSV. */
class AdminController extends Controller
{
    public function __construct(private readonly Content $content) {}

    public function dashboard(): View
    {
        $tools = $this->content->integrationTools();
        $types = array_column($this->content->form()['business_types'], 'label', 'id');

        $byStatus = BetaLead::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $counted = BetaLead::query()->counted()->get(['integrations', 'business_type', 'spend_range']);

        $votes = array_fill_keys(array_keys($tools), 0);
        $byType = array_fill_keys(array_keys($types), 0);
        foreach ($counted as $lead) {
            foreach ($lead->integrations ?? [] as $id) {
                if (array_key_exists($id, $votes)) {
                    $votes[$id]++;
                }
            }
            if ($lead->business_type && array_key_exists($lead->business_type, $byType)) {
                $byType[$lead->business_type]++;
            }
        }
        arsort($votes);

        $ranking = [];
        foreach ($votes as $id => $count) {
            $ranking[] = ['id' => $id, 'name' => $tools[$id]['name'], 'group' => $tools[$id]['group'], 'votes' => $count];
        }

        return view('admin.dashboard', [
            'byStatus' => $byStatus,
            'total' => array_sum($byStatus),
            'countedTotal' => $counted->count(),
            'ranking' => $ranking,
            'byType' => array_map(fn ($id, $c) => ['label' => $types[$id], 'count' => $c], array_keys($byType), $byType),
            'abandoned' => BetaLead::query()->where('status', BetaLead::STATUS_INCOMPLETE)
                ->selectRaw('last_step, count(*) as c')->groupBy('last_step')->orderBy('last_step')->pluck('c', 'last_step')->all(),
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $columns = [
            'id', 'email', 'status', 'last_step', 'business_type', 'business_type_other', 'integrations',
            'missing_text', 'spend_range', 'consent_contact_at', 'consent_contact_text', 'consent_marketing_at',
            'consent_marketing_text', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
            'referrer', 'created_at', 'completed_at', 'confirmed_at', 'unsubscribed_at',
        ];

        return response()->streamDownload(function () use ($columns) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM dla Excela
            fputcsv($out, $columns, ';', '"', '\\', "\r\n");

            BetaLead::query()->orderBy('id')->chunk(500, function ($leads) use ($out, $columns) {
                foreach ($leads as $lead) {
                    $row = [];
                    foreach ($columns as $col) {
                        $value = $lead->{$col};
                        if (is_array($value)) {
                            $value = implode(',', $value);
                        } elseif ($value instanceof \DateTimeInterface) {
                            $value = $value->format('Y-m-d H:i:s');
                        }
                        $row[] = (string) $value;
                    }
                    fputcsv($out, $row, ';', '"', '\\', "\r\n");
                }
            });
            fclose($out);
        }, 'zgloszenia-beta-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
