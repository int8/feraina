<section class="section bg-paper-dim">
    <div class="container-x grid gap-10 lg:grid-cols-[1fr_2fr] lg:gap-16">
        <h2 class="section-title">{{ $site['poland']['heading'] }}</h2>
        <ul class="rule-top">
            @foreach ($site['poland']['items'] as $item)
                <li class="flex items-baseline justify-between gap-6 border-b border-line py-5">
                    <p class="text-lg font-medium sm:text-xl">{{ $item['text'] }}</p>
                    @if (! empty($item['status']))
                        <span class="stamp shrink-0">{{ $item['status'] }}</span>
                    @else
                        <span class="shrink-0 font-mono text-sm text-stone">tak</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
