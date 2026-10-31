<?php

namespace App\Support;

use Illuminate\Support\HtmlString;

/**
 * Logotypy marek przy nazwach narzędzi (content/brands.json).
 * Zasada ze strony: gdzie pada nazwa firmy lub usługi, obok stoi jej miniaturka; gdy logo nieznane, literki na kafelku.
 * Wszystko jako inline SVG z atrybutami prezentacyjnymi, bo CSP nie dopuszcza atrybutów style="".
 */
final class Brands
{
    private static ?array $map = null;

    /** @var array<string, string|null> ścieżka <path d> per plik ikony */
    private static array $paths = [];

    private static ?string $pattern = null;

    /** @var array<string, string> rdzeń nazwy => id marki */
    private static array $stems = [];

    public static function all(): array
    {
        if (self::$map === null) {
            $raw = json_decode(file_get_contents(base_path('content/brands.json')), true, 512, JSON_THROW_ON_ERROR);
            unset($raw['_']);
            self::$map = $raw;
        }

        return self::$map;
    }

    public static function has(string $id): bool
    {
        return isset(self::all()[$id]);
    }

    /** Ikona marki: logo z simple-icons albo literki na kafelku w kolorze marki. */
    public static function icon(string $id, string $class = 'brand-ico'): HtmlString
    {
        $b = self::all()[$id] ?? null;
        if (! $b) {
            return new HtmlString('');
        }
        if (! empty($b['parts'])) {
            return new HtmlString(implode('', array_map(fn ($p) => self::icon($p, $class)->toHtml(), $b['parts'])));
        }

        $title = e($b['name']);
        $path = self::path($b['icon'] ?? null);
        if ($path) {
            return new HtmlString(sprintf(
                '<svg class="%s" viewBox="0 0 24 24" fill="%s" role="img" aria-label="%s"><path d="%s"/></svg>',
                e($class), e($b['color']), $title, e($path),
            ));
        }

        $letters = $b['letters'] ?? mb_strtoupper(mb_substr($b['name'], 0, 1));
        $size = match (true) {
            mb_strlen($letters) <= 1 => 14,
            mb_strlen($letters) === 2 => 11.5,
            mb_strlen($letters) === 3 => 9,
            default => 7.5,
        };

        return new HtmlString(sprintf(
            '<svg class="%s" viewBox="0 0 24 24" role="img" aria-label="%s"><rect width="24" height="24" rx="5" fill="%s"/><text x="12" y="12" dy=".36em" text-anchor="middle" font-family="Inter Variable, Inter, Arial, sans-serif" font-weight="700" font-size="%s" letter-spacing="-0.3" fill="%s">%s</text></svg>',
            e($class), $title, e($b['color']), $size, e($b['fg'] ?? '#ffffff'), e($letters),
        ));
    }

    /** Zwykły tekst → HTML: nazwy marek (z polską odmianą) dostają ikonę; reszta jest escapowana. */
    public static function mark(string $text): HtmlString
    {
        $escaped = e($text);
        $pattern = self::pattern();
        if ($pattern === '') {
            return new HtmlString($escaped);
        }

        return new HtmlString(preg_replace_callback($pattern, function (array $m): string {
            $id = self::$stems[$m[1]];

            return '<span class="brand">'.self::icon($id)->toHtml().$m[0].'</span>';
        }, $escaped));
    }

    private static function pattern(): string
    {
        if (self::$pattern !== null) {
            return self::$pattern;
        }
        foreach (self::all() as $id => $b) {
            if (array_key_exists('match', $b) && $b['match'] === null) {
                continue;
            }
            self::$stems[$b['match'] ?? $b['name']] = $id;
        }
        $stems = array_keys(self::$stems);
        usort($stems, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $alts = implode('|', array_map(fn ($s) => preg_quote(e($s), '/'), $stems));

        return self::$pattern = $alts === '' ? '' : '/(?<![\p{L}\d])('.$alts.')(\p{L}*)/u';
    }

    private static function path(?string $slug): ?string
    {
        if (! $slug) {
            return null;
        }
        if (array_key_exists($slug, self::$paths)) {
            return self::$paths[$slug];
        }
        $file = resource_path("svg/brands/{$slug}.svg");
        $d = null;
        if (is_file($file) && preg_match('/\sd="([^"]+)"/', file_get_contents($file), $m)) {
            $d = $m[1];
        }

        return self::$paths[$slug] = $d;
    }
}
