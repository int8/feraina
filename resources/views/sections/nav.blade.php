<header class="sticky top-0 z-40 border-b-2 border-rule bg-paper">
    <nav class="container-wide flex h-16 items-center justify-between gap-4" aria-label="Główna">
        <a href="{{ route('home') }}" class="flex items-center text-ink" aria-label="Udaina – strona główna">
            <x-logo-svg class="h-[18px] w-auto sm:h-5" />
        </a>

        <ul class="hidden items-center gap-7 md:flex">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}" class="text-base font-medium text-ink-soft hover:text-ink hover:underline hover:underline-offset-4">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <a href="{{ route('beta.show') }}" class="btn-stamp px-4 py-2 text-[15px]" data-open-beta data-plausible="cta_click" data-plausible-prop-location="nav">{{ $site['nav']['cta'] }}</a>
            <button type="button" class="p-2 md:hidden" data-nav-toggle aria-expanded="false" aria-controls="menu-mobilne" aria-label="Otwórz menu">
                <x-icon name="menu" />
            </button>
        </div>
    </nav>

    <div id="menu-mobilne" class="border-t border-line bg-paper md:hidden" data-nav-panel hidden>
        <ul class="container-wide flex flex-col divide-y divide-line">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}" class="block py-3 text-base font-medium text-ink-soft hover:text-ink">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</header>
