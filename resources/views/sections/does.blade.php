<section class="section">
    <div class="container-x">
        <h2 class="section-title">{{ $site['does']['heading'] }}</h2>
        <div class="rule-top mt-10 grid md:grid-cols-3 md:divide-x md:divide-line">
            @foreach ($site['does']['cards'] as $card)
                <article class="flex flex-col border-b border-line py-7 md:border-b-0 md:px-7 md:first:pl-0 md:last:pr-0">
                    <p class="eyebrow">{{ $card['title'] }}</p>
                    <h3 class="display mt-3 text-3xl sm:text-4xl">{{ $card['title'] }}</h3>
                    <p class="mt-4 flex-1 leading-relaxed text-ink-soft">{{ $card['text'] }}</p>
                    <p class="mt-6 font-mono text-sm text-ink">Oszczędzasz {{ $card['saved'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
