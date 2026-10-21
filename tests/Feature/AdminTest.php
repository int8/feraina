<?php

namespace Tests\Feature;

use App\Models\BetaLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['beta.admin.user' => 'admin', 'beta.admin.password' => 'sekret']);
    }

    private function seedLeads(): void
    {
        BetaLead::create(['email' => 'a@x.pl', 'status' => BetaLead::STATUS_CONFIRMED, 'business_type' => 'sklep', 'integrations' => ['allegro', 'ksef'], 'utm_source' => 'fb']);
        BetaLead::create(['email' => 'b@x.pl', 'status' => BetaLead::STATUS_COMPLETE, 'business_type' => 'sklep', 'integrations' => ['allegro']]);
        BetaLead::create(['email' => 'c@x.pl', 'status' => BetaLead::STATUS_INCOMPLETE, 'business_type' => 'b2b', 'integrations' => ['allegro', 'blik'], 'last_step' => 3]);
    }

    public function test_panel_requires_basic_auth(): void
    {
        $this->get('/panel')->assertStatus(401);
        $this->withBasicAuth('admin', 'zle')->get('/panel')->assertStatus(401);
        $this->withBasicAuth('admin', 'sekret')->get('/panel')->assertOk();
    }

    public function test_panel_is_hidden_when_not_configured(): void
    {
        config(['beta.admin.password' => null]);
        $this->withBasicAuth('admin', 'sekret')->get('/panel')->assertNotFound();
    }

    public function test_dashboard_ranks_integrations_from_complete_and_confirmed_only(): void
    {
        $this->seedLeads();
        $html = $this->withBasicAuth('admin', 'sekret')->get('/panel')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/Allegro<\/td>.*?<td[^>]*>2<\/td>/s', $html);
        $this->assertMatchesRegularExpression('/KSeF<\/td>.*?<td[^>]*>1<\/td>/s', $html);
        $this->assertMatchesRegularExpression('/BLIK<\/td>.*?<td[^>]*>0<\/td>/s', $html);
        $this->assertMatchesRegularExpression('/Sklep internetowy<\/td><td[^>]*>2<\/td>/', $html);
    }

    public function test_csv_export_contains_all_fields(): void
    {
        $this->seedLeads();
        $response = $this->withBasicAuth('admin', 'sekret')->get('/panel/eksport.csv')->assertOk();
        $csv = $response->streamedContent();

        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $header = explode(';', trim(explode("\r\n", ltrim($csv, "\xEF\xBB\xBF"))[0]));
        foreach (['email', 'status', 'last_step', 'business_type', 'integrations', 'missing_text', 'spend_range', 'consent_contact_at', 'consent_contact_text', 'consent_marketing_at', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content', 'referrer', 'created_at'] as $col) {
            $this->assertContains($col, $header, "Brak kolumny {$col}");
        }
        $this->assertStringContainsString('a@x.pl', $csv);
        $this->assertStringContainsString('allegro,ksef', $csv);
        $this->assertStringContainsString('fb', $csv);
        $this->assertSame(4, count(array_filter(explode("\r\n", $csv))));
    }

    public function test_csv_requires_auth(): void
    {
        $this->get('/panel/eksport.csv')->assertStatus(401);
    }
}
