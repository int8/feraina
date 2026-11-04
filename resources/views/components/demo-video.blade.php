@props(['base', 'label', 'eager' => false])
{{-- Film demo: na telefonie wersja pionowa (…-mobile, 9:16), wyżej pozioma (16:9). Źródła dopina app.js, więc ładuje się tylko jedna wersja;
     film w hero od razu (eager), pozostałe dopiero gdy zbliżają się do ekranu. Bez JS zostaje plakat. Nagrywanie: npm run record:all. --}}
<figure {{ $attributes->merge(['class' => 'hero-video']) }} aria-label="{{ $label }}">
    <video loop muted playsinline autoplay aria-hidden="true" tabindex="-1"
        preload="{{ $eager ? 'auto' : 'none' }}"
        poster="{{ asset("videos/{$base}-poster.jpg") }}"
        data-demo-video="{{ $base }}" @if ($eager) data-demo-eager @endif
        data-webm="{{ asset("videos/{$base}.webm") }}" data-mp4="{{ asset("videos/{$base}.mp4") }}"
        data-webm-mobile="{{ asset("videos/{$base}-mobile.webm") }}" data-mp4-mobile="{{ asset("videos/{$base}-mobile.mp4") }}"
        data-poster-mobile="{{ asset("videos/{$base}-mobile-poster.jpg") }}"></video>
</figure>
