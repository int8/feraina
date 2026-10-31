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
        <x-logo-mark class="hero-mark lamp" aria-hidden="true" />
        <h1 class="display" aria-label="{{ $heading }}"><span aria-hidden="true" data-scramble-root>{!! $chars !!}<span class="caret"></span></span></h1>
        <p class="hero-sub">{{ str_replace('*', '', $site['hero']['sub_lead']) }}</p>
        <div class="hero-actions">
            <a href="{{ route('beta.show') }}" class="btn-primary btn-lg" data-open-beta data-plausible="cta_click" data-plausible-prop-location="hero">{{ $site['hero']['cta_primary'] }}</a>
            <a href="#co" class="btn-secondary btn-lg">{{ $site['hero']['cta_secondary'] }}</a>
        </div>

        {{-- Dwa filmy do wyboru: „Web" (intro z /studio/intro) i „WhatsApp" (rozmowa z agentem z /studio/whatsapp).
             Nagrywane: npm run record:intro / record:whatsapp. Przełączanie zakładek: app.js. --}}
        <div class="hero-tabs" role="tablist" aria-label="Jak korzystasz z Udainy" data-hero-tabs>
            <button type="button" role="tab" id="hero-tab-web" class="hero-tab" aria-selected="true" aria-controls="hero-demo" data-hero-tab="web" data-plausible="hero_tab" data-plausible-prop-channel="web">
                <svg class="hero-tab-ico" viewBox="0 0 24 24" aria-hidden="true"><rect x="2.5" y="4.5" width="19" height="13" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M8.5 21h7M12 17.5V21" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                Web
            </button>
            <button type="button" role="tab" id="hero-tab-whatsapp" class="hero-tab" aria-selected="false" aria-controls="hero-demo" tabindex="-1" data-hero-tab="whatsapp" data-plausible="hero_tab" data-plausible-prop-channel="whatsapp">
                <x-brand id="whatsapp" class="brand-ico hero-tab-ico" />
                WhatsApp
            </button>
        </div>
        <div class="hero-video" id="hero-demo" role="tabpanel" aria-labelledby="hero-tab-web">
            <video autoplay loop muted playsinline preload="auto" poster="{{ asset('videos/intro-poster.jpg') }}" aria-hidden="true" tabindex="-1" data-hero-video="web">
                <source src="{{ asset('videos/intro.webm') }}" type="video/webm">
                <source src="{{ asset('videos/intro.mp4') }}" type="video/mp4">
            </video>
            <video loop muted playsinline preload="none" poster="{{ asset('videos/whatsapp-poster.jpg') }}" aria-hidden="true" tabindex="-1" data-hero-video="whatsapp" hidden>
                <source src="{{ asset('videos/whatsapp.webm') }}" type="video/webm">
                <source src="{{ asset('videos/whatsapp.mp4') }}" type="video/mp4">
            </video>
        </div>
    </div>
</section>
