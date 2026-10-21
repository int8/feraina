<section id="integracje" class="section">
    <div class="container-x">
        <h2 class="section-title">{{ $integrations['heading'] }}</h2>
        <p class="mt-4 max-w-[50ch] text-lg text-ink-soft">{{ $integrations['subheading'] }}</p>

        <div class="rule-top mt-10">
            @foreach ($integrations['groups'] as $group)
                <article class="grid gap-3 border-b border-line py-6 md:grid-cols-[minmax(0,1.2fr)_minmax(0,2fr)_auto] md:gap-8" data-integration-group="{{ $group['id'] }}">
                    <div>
                        <h3 class="text-xl font-semibold">{{ $group['name'] }}</h3>
                        <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $group['description'] }}</p>
                    </div>
                    <ul class="flex flex-wrap gap-x-3 gap-y-1 self-start font-mono text-sm text-ink-soft md:pt-1">
                        @foreach ($group['tools'] as $tool)
                            <li class="tag">{{ $tool['name'] }}@if (! $loop->last)<span class="text-line"> ·</span>@endif</li>
                        @endforeach
                    </ul>
                    <span class="stamp self-start justify-self-start md:justify-self-end">{{ $integrations['status_label'] }}</span>
                </article>
            @endforeach
        </div>
    </div>
</section>
