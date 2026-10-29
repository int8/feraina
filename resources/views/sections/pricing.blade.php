<section id="cennik" class="section center" style="background: var(--color-paper-dim)">
    <div class="wrap">
        <p class="eyebrow">{{ $site['pricing']['eyebrow'] }}</p>
        <h2 class="section-title">{{ $site['pricing']['heading'] }}</h2>
        <div class="pricing-grid">
            @foreach ($site['pricing']['plans'] as $plan)
                <div class="price-card {{ isset($plan['featured']) && $plan['featured'] ? 'featured' : '' }}">
                    <h3>{{ $plan['name'] }}</h3>
                    <div class="price-amt">{{ $plan['price'] }}<span>{{ $site['pricing']['period'] }}</span></div>
                    <ul>
                        @foreach ($plan['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="#zapis" class="btn-primary w-full font-extrabold tracking-wide" data-open-beta data-plausible="cta_click" data-plausible-prop-location="pricing">{{ $site['pricing']['cta'] }}</a>
                </div>
            @endforeach
        </div>
    </div>
</section>
