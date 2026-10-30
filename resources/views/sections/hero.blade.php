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
        <x-logo-mark class="hero-mark" aria-hidden="true" />
        <h1 class="display" aria-label="{{ $heading }}"><span aria-hidden="true" data-scramble-root>{!! $chars !!}</span></h1>
        <p class="hero-sub">{{ str_replace('*', '', $site['hero']['sub_lead']) }}</p>
        <div class="hero-actions">
            <a href="{{ route('beta.show') }}" class="btn-primary btn-lg" data-open-beta data-plausible="cta_click" data-plausible-prop-location="hero">{{ $site['hero']['cta_primary'] }}</a>
            <a href="#co" class="btn-secondary btn-lg">{{ $site['hero']['cta_secondary'] }}</a>
        </div>

        {{-- Film: intro z logo, kliknięcie „Utwórz firmę", potem przebieg pracy agentów. Nagrywany z /studio/intro (npm run record:intro). --}}
        <div class="hero-video">
            <video autoplay loop muted playsinline preload="auto" poster="{{ asset('videos/intro-poster.jpg') }}" aria-hidden="true" tabindex="-1" data-hero-video>
                <source src="{{ asset('videos/intro.webm') }}" type="video/webm">
                <source src="{{ asset('videos/intro.mp4') }}" type="video/mp4">
            </video>
        </div>
    </div>
</section>
