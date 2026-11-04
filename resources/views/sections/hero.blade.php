@php
    // Nagłówek litera po literze: JS podmienia znaki przez chwilę na losowe (efekt „scramble").
    // Fragment w *gwiazdkach* dostaje <span class="hl"> – motywy wyróżniają go kolorem, kursywą lub pigułką.
    $heading = str_replace('*', '', $site['hero']['heading']);
    $chars = '';
    foreach (preg_split('/(\*[^*]+\*)/u', $site['hero']['heading'], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $part) {
        $hl = str_starts_with($part, '*');
        if ($hl) {
            $part = trim($part, '*');
            $chars .= '<span class="hl">';
        }
        foreach (preg_split('//u', $part, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
            $chars .= $ch === ' ' ? '<span> </span>' : '<span data-scramble="'.e($ch).'">'.e($ch).'</span>';
        }
        if ($hl) {
            $chars .= '</span>';
        }
    }
@endphp
<section class="hero" id="top">
    <div class="wrap">
        <h1 class="display" aria-label="{{ $heading }}"><span aria-hidden="true" data-scramble-root>{!! $chars !!}<span class="caret"></span></span></h1>
        <p class="hero-sub">{{ str_replace('*', '', $site['hero']['sub_lead']) }}</p>

        <div class="hero-stage">
        <x-demo-video base="intro" label="Demo: Udaina buduje i publikuje stronę klienta" :eager="true" />

        </div>
    </div>
</section>
