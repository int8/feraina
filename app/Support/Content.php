<?php

namespace App\Support;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Ładuje treści strony z katalogu content/ (JSON i Markdown).
 * Copy i lista integracji są w plikach, nie w komponentach.
 */
class Content
{
    /** @var array<string, mixed> */
    private array $cache = [];

    public function __construct(private readonly string $basePath) {}

    /** @return array<string, mixed> */
    public function site(): array
    {
        return $this->json('site');
    }

    /** @return array<string, mixed> */
    public function faq(): array
    {
        return $this->json('faq');
    }

    /** @return array<string, mixed> */
    public function form(): array
    {
        return $this->json('form');
    }

    /** @return array<string, mixed> */
    public function demo(): array
    {
        return $this->json('demo');
    }

    /** @return array<string, mixed> */
    public function integrations(): array
    {
        return $this->json('integrations');
    }

    /**
     * Płaska lista narzędzi: id => ['name' => ..., 'group' => ..., 'group_id' => ...].
     *
     * @return array<string, array{name: string, group: string, group_id: string}>
     */
    public function integrationTools(): array
    {
        $tools = [];
        foreach ($this->integrations()['groups'] as $group) {
            foreach ($group['tools'] as $tool) {
                $tools[$tool['id']] = [
                    'name' => $tool['name'],
                    'group' => $group['name'],
                    'group_id' => $group['id'],
                ];
            }
        }

        return $tools;
    }

    /** @return list<string> */
    public function integrationIds(): array
    {
        return array_keys($this->integrationTools());
    }

    /** @return list<string> */
    public function businessTypeIds(): array
    {
        return array_column($this->form()['business_types'], 'id');
    }

    /** @return list<string> */
    public function spendRangeIds(): array
    {
        return array_column($this->form()['spend_ranges'], 'id');
    }

    public function legalHtml(string $slug): string
    {
        $path = $this->basePath.'/legal/'.$slug.'.md';
        if (! is_file($path)) {
            throw new RuntimeException("Brak pliku treści: {$path}");
        }

        return (string) Str::markdown((string) file_get_contents($path), ['html_input' => 'strip', 'allow_unsafe_links' => false]);
    }

    /**
     * Podmienia placeholdery produktu (np. [X], [NAZWA]) w dowolnym tekście.
     */
    public function fill(string $text): string
    {
        $site = $this->site();
        $map = [
            '[X]' => $site['product']['beta_slots'] ?? '[X]',
            '[NAZWA]' => $site['product']['name'] ?? '[NAZWA]',
            '[NAZWA FIRMY]' => $site['company']['name'] ?? '[NAZWA FIRMY]',
            '[NIP]' => $site['company']['nip'] ?? '[NIP]',
            '[ADRES]' => $site['company']['address'] ?? '[ADRES]',
            '[E-MAIL KONTAKTOWY]' => $site['company']['email'] ?? '[E-MAIL KONTAKTOWY]',
        ];

        return strtr($text, $map);
    }

    /** @return array<string, mixed> */
    private function json(string $name): array
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $path = $this->basePath.'/'.$name.'.json';
        if (! is_file($path)) {
            throw new RuntimeException("Brak pliku treści: {$path}");
        }

        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return $this->cache[$name] = $data;
    }
}
