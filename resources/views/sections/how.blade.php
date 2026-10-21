<section id="jak-to-dziala" class="section">
    <div class="container-x">
        <h2 class="section-title">{{ $site['how']['heading'] }}</h2>
        <ol class="rule-top mt-10 grid md:grid-cols-3 md:divide-x md:divide-line">
            @foreach ($site['how']['steps'] as $i => $step)
                <li class="border-b border-line py-7 md:border-b-0 md:px-7 md:first:pl-0 md:last:pr-0">
                    <span class="display block text-6xl text-ink sm:text-7xl" aria-hidden="true">{{ $i + 1 }}</span>
                    <h3 class="mt-5 text-xl font-semibold"><span class="sr-only">Krok {{ $i + 1 }}: </span>{{ $step['title'] }}</h3>
                    <p class="mt-2 leading-relaxed text-ink-soft">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
