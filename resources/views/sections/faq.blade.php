<section id="faq" class="section">
    <div class="container-x grid gap-10 lg:grid-cols-[1fr_2fr] lg:gap-16">
        <h2 class="section-title">{{ $faq['heading'] }}</h2>
        <div class="rule-top">
            @foreach ($faq['items'] as $item)
                <details class="group border-b border-line">
                    <summary class="flex cursor-pointer list-none items-baseline justify-between gap-6 py-5 text-lg font-medium">
                        {{ $item['question'] }}
                        <span class="shrink-0 font-mono text-xl text-stone group-open:hidden" aria-hidden="true">+</span>
                        <span class="hidden shrink-0 font-mono text-xl text-stone group-open:inline" aria-hidden="true">−</span>
                    </summary>
                    <p class="max-w-[62ch] pb-6 leading-relaxed text-ink-soft">{{ $content->fill($item['answer']) }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
