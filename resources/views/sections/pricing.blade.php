<section id="cennik" class="section center" style="background: var(--color-paper-dim)">
    <div class="wrap">
        <p class="eyebrow">{{ $site['pricing']['eyebrow'] }}</p>
        <h2 class="section-title">{{ $site['pricing']['heading'] }}</h2>
        <div class="price">
            <div class="price-amt">{{ $site['pricing']['price'] }}<span>{{ $site['pricing']['period'] }}</span></div>
            <ul>
                @foreach ($site['pricing']['features'] as $feature)
                    <li>{{ $feature }}</li>
                @endforeach
            </ul>
            <a href="#zapis" class="btn-primary w-full" data-open-beta data-plausible="cta_click" data-plausible-prop-location="pricing">{{ $site['pricing']['cta'] }}</a>
        </div>
    </div>
</section>
