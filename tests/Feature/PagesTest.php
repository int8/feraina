<?php

namespace Tests\Feature;

use App\Models\BetaLead;
use App\Support\Content;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_renders_all_sections_in_order(): void
    {
        $response = $this->get('/')->assertOk();
        $html = $response->getContent();

        $markers = [
            'aria-label="Biznes w pięć minut"',
            'data-scramble="B"',
            'videos/intro.mp4',
            'id="zasady"',
            'id="co"',
            'Cztery obszary',
            'id="integracje"',
            'id="dzien"',
            'Co się dzieje, kiedy Ty prowadzisz firmę',
            'id="cennik"',
            'id="faq"',
            'id="zapis"',
            'Polityka prywatności',
        ];
        $pos = -1;
        foreach ($markers as $marker) {
            $next = mb_strpos($html, $marker, $pos + 1);
            $this->assertNotFalse($next, "Brak sekcji: {$marker}");
            $this->assertGreaterThan($pos, $next, "Zła kolejność: {$marker}");
            $pos = $next;
        }
    }

    public function test_home_does_not_show_signup_results_or_survey_questions(): void
    {
        $lead = BetaLead::create(['email' => 'x@y.pl', 'status' => BetaLead::STATUS_CONFIRMED, 'integrations' => ['allegro']]);

        $this->get('/')->assertOk()
            ->assertDontSee('Które integracje są dla Ciebie najważniejsze')
            ->assertDontSee('Ranking')
            ->assertDontSee('x@y.pl');
    }

    public function test_integrations_from_config_drive_section_and_form(): void
    {
        $content = app(Content::class);
        $groups = $content->integrations()['groups'];
        $this->assertCount(7, $groups);

        $this->startSurvey();
        $survey = $this->get(route('beta.survey'))->assertOk();

        foreach ($groups as $group) {
            $survey->assertSee($group['name']);
            foreach ($group['tools'] as $tool) {
                $survey->assertSee('value="'.$tool['id'].'"', false);
            }
        }
    }

    public function test_changed_integrations_file_updates_section_and_form(): void
    {
        $dir = sys_get_temp_dir().'/tornado-content-'.uniqid();
        mkdir($dir);
        mkdir($dir.'/legal');
        foreach (['site', 'faq', 'form', 'demo'] as $f) {
            copy(base_path("content/{$f}.json"), "{$dir}/{$f}.json");
        }
        $custom = [
            'heading' => 'Nagłówek testowy', 'subheading' => 'Podtytuł', 'status_label' => 'w planach', 'max_selected' => 3,
            'groups' => [[
                'id' => 'testowa', 'name' => 'Grupa Testowa', 'description' => 'Opis testowy.',
                'tools' => [['id' => 'narzedzie-x', 'name' => 'Narzędzie X']],
            ]],
        ];
        file_put_contents("{$dir}/integrations.json", json_encode($custom, JSON_UNESCAPED_UNICODE));
        $this->app->singleton(Content::class, fn () => new Content($dir));

        $this->startSurvey();
        $this->get(route('beta.survey'))->assertOk()->assertSee('value="narzedzie-x"', false)->assertDontSee('value="allegro"', false);

        // Walidacja też korzysta z pliku: stare id jest odrzucone, nowe przyjęte.
        $this->postJson(route('beta.progress'), ['last_step' => 2, 'integrations' => ['allegro']])->assertStatus(422);
        $this->postJson(route('beta.progress'), ['last_step' => 2, 'integrations' => ['narzedzie-x']])->assertOk();
    }

    public function test_legal_pages_render_from_markdown(): void
    {
        $this->get('/polityka-prywatnosci')->assertOk()->assertSee('<h1>Polityka prywatności</h1>', false);
        $this->get('/regulamin')->assertOk()->assertSee('<h1>Regulamin</h1>', false);
    }

    public function test_security_headers_are_set(): void
    {
        $this->get('/')->assertOk()
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
        $csp = $this->get('/')->headers->get('Content-Security-Policy');
        $this->assertStringContainsString("default-src 'self'", $csp);
        $this->assertStringContainsString("frame-ancestors 'none'", $csp);

        $this->get('https://localhost/')->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }

    public function test_plausible_script_only_when_configured(): void
    {
        $this->get('/')->assertDontSee('plausible.io');
        config(['beta.plausible.domain' => 'example.pl']);
        $this->get('/')->assertSee('data-domain="example.pl"', false);
        $this->assertStringContainsString('https://plausible.io', $this->get('/')->headers->get('Content-Security-Policy'));
    }

    private function startSurvey(): void
    {
        $this->post(route('beta.start'), ['email' => 'test@example.pl'])->assertRedirect(route('beta.survey'));
    }
}
