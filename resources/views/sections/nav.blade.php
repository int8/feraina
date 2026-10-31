<header class="nav">
    <nav class="wrap nav-inner" aria-label="Główna">
        <a href="{{ route('home') }}" class="logo" aria-label="Udaina – strona główna">
            <x-logo-mark class="lamp" aria-hidden="true" />
        </a>

        <ul class="nav-links hidden items-center gap-7 md:flex">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <a href="{{ route('beta.show') }}" class="btn-primary" data-open-beta data-plausible="cta_click" data-plausible-prop-location="nav">{{ $site['nav']['cta'] }}</a>
            <button type="button" class="p-2 md:hidden" data-nav-toggle aria-expanded="false" aria-controls="menu-mobilne" aria-label="Otwórz menu">
                <x-icon name="menu" />
            </button>
        </div>
    </nav>

    <div id="menu-mobilne" class="nav-mobile md:hidden" data-nav-panel hidden>
        <ul class="wrap divide-y divide-line">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</header>
