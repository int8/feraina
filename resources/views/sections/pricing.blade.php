<section class="section" id="cennik">
    <div class="wrap">
        <div class="sec-head center">
            <p class="eyebrow">{{ $site['pricing']['eyebrow'] }}</p>
            <h2 class="section-title">{{ $site['pricing']['heading'] }}</h2>
        </div>
        <div class="plans">
            @foreach ($site['pricing']['plans'] as $i => $plan)
                @php($featured = ! empty($plan['featured']))
                <article class="plan {{ $featured ? 'featured' : '' }}">
                    <div class="k"><span>{{ sprintf('%02d', $i + 1) }} · {{ $plan['name'] }}</span>@if ($featured)<b>Najczęściej wybierany</b>@endif</div>
                    <div class="amt">{{ $plan['price'] }}</div>
                    <div class="per">{{ $site['pricing']['period'] }}</div>
                    <p class="plan-for">{{ $plan['for'] }}</p>
                    <ul>
                        @foreach ($plan['features'] as $feature)
                            <li>
                                <div>
                                    <strong>{!! \App\Support\Brands::mark($feature['title']) !!}</strong>
                                    @if (! empty($feature['text']))
                                        <span class="plan-desc">{!! \App\Support\Brands::mark($feature['text']) !!}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <a href="#signup" class="{{ $featured ? 'btn-primary' : 'btn-secondary' }}" data-plausible="cta_click" data-plausible-prop-location="pricing">{{ $site['pricing']['cta'] }}</a>
                </article>
            @endforeach
        </div>
    </div>
</section>
