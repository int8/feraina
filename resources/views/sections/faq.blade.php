<section id="faq" class="section center">
    <div class="wrap">
        <p class="eyebrow">{{ $site['faq']['eyebrow'] }}</p>
        <h2 class="section-title">{{ $site['faq']['heading'] }}</h2>
        <div class="faq">
            @foreach ($site['faq']['items'] as $item)
                <details>
                    <summary>{{ $item['question'] }}</summary>
                    <p>{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
