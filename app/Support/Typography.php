<?php

namespace App\Support;

/** Polska typografia: jednoliterowe spójniki i przyimki (a, i, o, u, w, z) nie zostają na końcu wiersza. */
final class Typography
{
    /** Po samotnej literze wstawia twardą spację, także w ciągach („i w Google”) i na końcu węzła tekstowego
     *  (gdy dalej stoi element liniowy, np. ikona marki). */
    public static function orphans(string $text): string
    {
        $glued = preg_replace('/(?<=^|[\s(„"»\x{00A0}])([aiouwzAIOUWZ])\s+(?=\S|\z)/u', "$1\u{00A0}", $text);

        return $glued ?? $text;
    }

    /** To samo dla gotowego HTML: tylko węzły tekstowe, z pominięciem script/style/textarea/pre. */
    public static function html(string $html): string
    {
        $result = preg_replace_callback(
            '/<(script|style|textarea|pre)\b[^>]*>.*?<\/\1\s*>|>([^<]+)</isu',
            fn (array $m): string => isset($m[2]) ? '>'.self::orphans($m[2]).'<' : $m[0],
            $html,
        );

        return $result ?? $html;
    }
}
