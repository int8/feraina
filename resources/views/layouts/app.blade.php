<!DOCTYPE html>
<html lang="pl" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' · ' : '' }}{{ $content->fill($site['product']['name']) }} – {{ $site['product']['tagline'] }}</title>
    <meta name="description" content="{{ $content->fill($site['product']['meta_description']) }}">
    <meta name="color-scheme" content="light">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    @if (config('beta.plausible.domain'))
        <script defer data-domain="{{ config('beta.plausible.domain') }}" src="{{ config('beta.plausible.script') }}"></script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    <a href="#tresc" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Przejdź do treści</a>

    @include('sections.nav')

    <main id="tresc" class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @include('sections.footer')
</body>
</html>
