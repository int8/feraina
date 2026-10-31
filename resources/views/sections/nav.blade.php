<header class="nav">
    <nav class="wrap nav-inner" aria-label="Główna">
        <a href="#zapis" class="btn-primary nav-cta" data-plausible="cta_click" data-plausible-prop-location="nav">{{ $site['nav']['cta'] }}</a>

        <ul class="nav-links hidden items-center gap-7 md:flex">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>

        <button type="button" class="nav-toggle p-2 md:hidden" data-nav-toggle aria-expanded="false" aria-controls="menu-mobilne" aria-label="Otwórz menu">
            <x-icon name="menu" />
        </button>
    </nav>

    <div id="menu-mobilne" class="nav-mobile md:hidden" data-nav-panel hidden>
        <ul class="wrap divide-y divide-line">
            @foreach ($site['nav']['links'] as $link)
                <li><a href="{{ route('home') }}{{ $link['href'] }}">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</header>
