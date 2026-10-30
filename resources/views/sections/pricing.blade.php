<section class="section" id="cennik">
    <div class="wrap">
        <div class="sec-head center">
            <p class="eyebrow">{{ $site['pricing']['eyebrow'] }}</p>
            <h2 class="section-title" data-reveal>{{ $site['pricing']['heading'] }}</h2>
        </div>
        <div class="plans">
            @foreach ($site['pricing']['plans'] as $i => $plan)
                @php($featured = ! empty($plan['featured']))
                <article class="plan {{ $featured ? 'featured' : '' }}">
                    <div class="k"><span>{{ sprintf('%02d', $i + 1) }} · {{ $plan['name'] }}</span>@if ($featured)<b>Najczęściej wybierany</b>@endif</div>
                    <div class="amt">{{ $plan['price'] }}</div>
                    <div class="per">{{ $site['pricing']['period'] }}</div>
                    <ul>
                        @foreach ($plan['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('beta.show') }}" class="{{ $featured ? 'btn-primary' : 'btn-secondary' }}" data-open-beta data-plausible="cta_click" data-plausible-prop-location="pricing">{{ $site['pricing']['cta'] }}</a>
                </article>
            @endforeach
        </div>
    </div>
</section>
