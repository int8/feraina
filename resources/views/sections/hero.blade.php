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
        <div class="hero-stage">
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

        {{-- Robocik: spaceruje po dolnej krawędzi filmu (prawy róg) i myśli o 🤡 xd. Czysta ozdoba, tylko CSS. --}}
        <div class="bot" aria-hidden="true">
            <div class="bot-think">🤡 xd</div>
            <svg class="bot-fig" viewBox="0 0 120 150">
                <g class="bot-leg bot-leg-l">
                    <path d="M46 118v20" stroke="#0b0c0e" stroke-width="13" stroke-linecap="round"/>
                    <path d="M46 118v20" stroke="#8ea2ff" stroke-width="6" stroke-linecap="round"/>
                    <rect x="31" y="134" width="30" height="13" rx="6.5" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                </g>
                <g class="bot-leg bot-leg-r">
                    <path d="M74 118v20" stroke="#0b0c0e" stroke-width="13" stroke-linecap="round"/>
                    <path d="M74 118v20" stroke="#8ea2ff" stroke-width="6" stroke-linecap="round"/>
                    <rect x="59" y="134" width="30" height="13" rx="6.5" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                </g>
                <g class="bot-arm bot-arm-l">
                    <path d="M30 86 22 112" stroke="#0b0c0e" stroke-width="13" stroke-linecap="round"/>
                    <path d="M30 86 22 112" stroke="#8ea2ff" stroke-width="6" stroke-linecap="round"/>
                </g>
                <g class="bot-arm bot-arm-r">
                    <path d="M90 86 98 112" stroke="#0b0c0e" stroke-width="13" stroke-linecap="round"/>
                    <path d="M90 86 98 112" stroke="#8ea2ff" stroke-width="6" stroke-linecap="round"/>
                </g>
                <rect x="50" y="66" width="20" height="16" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                <rect x="28" y="78" width="64" height="44" rx="14" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                <circle class="bot-light" cx="60" cy="99" r="5" fill="#ff7a30"/>
                <g class="bot-head">
                    <path d="M60 12V5" stroke="#0b0c0e" stroke-width="4" stroke-linecap="round"/>
                    <circle class="bot-light" cx="60" cy="5" r="4.5" fill="#ff7a30"/>
                    <rect x="12" y="32" width="12" height="24" rx="5" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                    <rect x="96" y="32" width="12" height="24" rx="5" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                    <rect x="22" y="12" width="76" height="62" rx="18" fill="#8ea2ff" stroke="#0b0c0e" stroke-width="4"/>
                    <rect x="38" y="32" width="46" height="26" rx="9" fill="#e9ecff" stroke="#0b0c0e" stroke-width="4"/>
                    <path class="bot-eyes" d="M49 45h8M63 45h8" stroke="#0b0c0e" stroke-width="4" stroke-linecap="round"/>
                </g>
            </svg>
        </div>
        </div>
    </div>
</section>
