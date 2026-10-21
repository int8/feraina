<section id="noc" class="section bg-paper-dim">
    <div class="container-x">
        <h2 class="section-title max-w-3xl">{{ $site['night']['heading'] }}</h2>

        <div class="mt-10 border-2 border-rule bg-night text-sm text-paper sm:text-base" data-night-log>
            <div class="flex items-center justify-between border-b border-night-line px-4 py-2 font-mono text-xs text-line">
                <span>raport-nocy.log</span>
                <span>22:00 → 06:00</span>
            </div>
            <ol class="space-y-3 p-5 font-mono sm:p-8">
                @foreach ($site['night']['entries'] as $entry)
                    <li class="night-log-entry flex gap-5">
                        <span class="shrink-0 text-signal tabular-nums">{{ $entry['time'] }}</span>
                        <span>{{ $entry['text'] }}</span>
                    </li>
                @endforeach
                <li class="font-mono text-stone" aria-hidden="true"><span data-night-cursor></span></li>
            </ol>
        </div>
        <p class="mt-3 font-mono text-xs uppercase tracking-[0.12em] text-stone">{{ $site['night']['caption'] }}</p>
    </div>
</section>
