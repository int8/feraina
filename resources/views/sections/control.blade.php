<section class="section bg-night text-paper">
    <div class="container-x grid gap-10 lg:grid-cols-[1fr_2fr] lg:gap-16">
        <h2 class="section-title text-paper">{{ $site['control']['heading'] }}</h2>
        <ul class="border-t-2 border-paper">
            @foreach ($site['control']['items'] as $i => $item)
                <li class="flex items-baseline gap-5 border-b border-night-line py-5">
                    <span class="font-mono text-sm text-signal" aria-hidden="true">■</span>
                    <p class="text-lg font-medium sm:text-xl">{{ $item }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
