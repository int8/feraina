<footer class="footer">
    <div class="wrap footer-inner">
        <a href="{{ route('home') }}" class="logo" aria-label="Biznes w 5 minut – strona główna">
            <x-logo-mark class="h-5 w-5" aria-hidden="true" />
            <span>Biznes w 5 minut</span>
        </a>
        <ul class="flex flex-wrap gap-x-6 gap-y-2">
            @foreach ($site['footer']['links'] as $link)
                <li><a href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
        <span>{{ $site['product']['domain'] }} · {{ date('Y') }}</span>
    </div>
</footer>
