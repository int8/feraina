<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1920">
    <meta name="robots" content="noindex">
    <title>Studio · WhatsApp</title>
    @vite(['resources/css/studio.css', 'resources/js/studio-whatsapp.js'])
</head>
<body class="studio">
{{--
    Scena 1920×1080 do nagrania drugiego filmu z hero (zakładka „WhatsApp”, npm run record:whatsapp).
    Telefon z rozmową na WhatsAppie: właściciel pyta, jak idzie praca → agent przysyła raport →
    prosi o decyzję w sprawie kampanii Google Ads pod wykryty trend → „Tak, ale limit…” → potwierdzenie.
    Treść wiadomości i przebieg: resources/js/studio-whatsapp.js. Podpisy po lewej są poza .scene, więc kamera ich nie rusza.
--}}
<script type="application/json" id="brand-icons">{!! json_encode(collect(['google-ads', 'blik', 'przelewy24'])->mapWithKeys(fn ($id) => [$id => \App\Support\Brands::icon($id)->toHtml()])) !!}</script>

<div class="scene" id="scene">
    <div class="desk"></div>

    <div class="wa-phone" id="phone">
        <div class="phone-frame"></div>
        <div class="phone-screen">
            <span class="phone-island"></span>
            <div class="phone-status"><span>14:32</span><span class="phone-sig"><i></i><i></i><i></i></span></div>
            <div class="wa">
                <header class="wa-head">
                    <span class="wa-back"><svg viewBox="0 0 12 20" aria-hidden="true"><path d="M10 2 2 10l8 8" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg><b>3</b></span>
                    <span class="wa-av"><x-logo-mark class="lamp" /></span>
                    <span class="wa-who"><b>Udaina</b><span id="wa-sub">online</span></span>
                    <span class="wa-acts">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="2.5" y="6" width="13" height="12" rx="3" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="m15.5 10 5-3v10l-5-3z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6.6 3.5 9 7.5l-1.8 1.8a12 12 0 0 0 7.5 7.5L16.5 15l4 2.4-1 3.1C12 21.5 2.5 12 3.5 4.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                    </span>
                </header>
                <div class="wa-body" id="wa-body">
                    <div class="wa-list" id="wa-list"></div>
                </div>
                <footer class="wa-foot" id="wa-foot">
                    <span class="wa-plus"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 4v16M4 12h16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/></svg></span>
                    <span class="wa-input"><span id="wa-typed"></span><i class="wa-caret"></i><span class="wa-ph">Wiadomość</span><svg class="wa-sticker" viewBox="0 0 24 24" aria-hidden="true"><path d="M20 12a8 8 0 1 1-8-8h3l5 5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M15 4v5h5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="9" cy="11" r="1.1" fill="currentColor"/><circle cx="14" cy="13" r="1.1" fill="currentColor"/><path d="M8.5 15.5c1.8 1.6 4 1.6 6 0" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg></span>
                    <span class="wa-cam"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 8.5A2.5 2.5 0 0 1 6.5 6H8l1.3-2h5.4L16 6h1.5A2.5 2.5 0 0 1 20 8.5v8a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 16.5z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12.5" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/></svg></span>
                    <span class="wa-mic" id="wa-send">
                        <svg class="ic-mic" viewBox="0 0 24 24" aria-hidden="true"><rect x="9" y="3" width="6" height="11" rx="3" fill="currentColor"/><path d="M6 11a6 6 0 0 0 12 0M12 17v4M9 21h6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                        <svg class="ic-send" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4l16 8-16 8 2.5-8z" fill="currentColor"/></svg>
                    </span>
                </footer>
                <span class="wa-home"></span>
            </div>
        </div>
    </div>
</div>

{{-- Podpisy po lewej: poza .scene, więc nie podlegają kamerze. --}}
<div class="wa-cap" id="cap">
    <span class="wa-cap-k"><x-brand id="whatsapp" />WhatsApp</span>
    <span class="wa-cap-t" id="cap-t"></span>
    <span class="wa-cap-s" id="cap-s"></span>
</div>
</body>
</html>
