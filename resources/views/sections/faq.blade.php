<section class="section" id="faq">
    <div class="wrap">
        <div class="faq-grid">
            <div class="sec-head">
                <p class="eyebrow">{{ $site['faq']['eyebrow'] }}</p>
                <h2 class="section-title" data-reveal>{{ $site['faq']['heading'] }}</h2>
                <p class="lead">{{ $site['faq']['lead'] }}</p>
            </div>
            <div class="faq-list">
                @foreach ($site['faq']['items'] as $i => $item)
                    <details>
                        <summary><span class="n">{{ sprintf('%02d', $i + 1) }}</span><span>{{ $item['question'] }}</span></summary>
                        <p>{!! \App\Support\Brands::mark($item['answer']) !!}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </div>
</section>
