<section class="pt-10 pb-16 sm:pt-14 sm:pb-20 lg:pt-16 lg:pb-24">
    <div class="container-wide">
        <h1 class="display text-[2.5rem] sm:text-6xl lg:text-[5.5rem]">{{ $site['hero']['heading'] }}.</h1>
    </div>

    {{-- Mock dashboardu: szerszy niż kolumna tekstu. Stan spoczynkowy renderuje serwer, JS dokłada ruch. --}}
    @php($dash = $demo['sessions'][0] + ['status' => $demo['dashboard']['status']])
    @php($initialTasks = array_slice(array_values(array_filter($dash['tasks'], fn ($t) => empty($t['decision']))), 0, 2))
    @php($initialDecision = array_values(array_filter($dash['tasks'], fn ($t) => ! empty($t['decision'])))[0] ?? null)
    @php($dash['today'] = [['text' => 'Raport gotowy. 4 zadania, z czego 1 czeka na Twoją decyzję. Reszta wdrożona i zapisana w logu.', 'time' => 'raport']])
    <div class="container-wide mt-10 lg:mt-14">
        <div class="demo" data-demo role="region" aria-label="Przykładowy panel Ferajny">
            <div class="demo-top">
                <ol class="demo-strip" data-demo-strip aria-live="off">
                    <li class="demo-strip-line is-start">> {{ $dash['intro'][0] }}</li>
                    <li class="demo-strip-line is-start">> Zadanie rozpoczęte: {{ $initialTasks[0]['start'] }}</li>
                    <li class="demo-strip-line is-done">> Zakończone: {{ $initialTasks[0]['done'] }}</li>
                </ol>
                {{-- Debug: surowy ruch pod spodem. Sekrety zawsze zamaskowane. --}}
                <ol class="demo-debug" data-demo-debug aria-hidden="true">
                    <li><time>09:41:02.118</time>GET https://gmail.googleapis.com/gmail/v1/users/me/messages?q=is:unread 200 142ms</li>
                    <li><time>09:41:02.301</time>auth · Bearer •••••••••••• (ukryte)</li>
                    <li><time>09:41:02.870</time>llm · 1 240 tok → 186 tok · 1,4s</li>
                </ol>
            </div>

            <div class="demo-bar">
                <span class="wide text-base font-extrabold uppercase" data-demo-company>{{ $dash['company'] }}</span>
                <span class="demo-bar-stats">
                    <span>etap <b data-demo-step>raport</b></span>
                    <span>wydane w sesji <b data-demo-budget>50,20 / {{ $demo['budget_limit'] }} zł</b></span>
                    <span class="is-red">do decyzji <b data-demo-decisions>1</b></span>
                </span>
            </div>

            <div class="demo-body">
                {{-- Kolumna 1: firma --}}
                <div class="demo-col" data-demo-col="firma">
                    <h3 class="demo-col-title">Firma</h3>
                    <p class="demo-col-hint">Liczby i dokumenty, które Ferajna pilnuje za Ciebie.</p>
                    <div class="demo-firma" data-demo-firma>
                    <div class="demo-ship" data-demo-ship><b>{{ $dash['status']['label'] }}</b><span>{{ $dash['status']['text'] }}</span></div>
                    <dl class="demo-metrics">
                        @foreach ($dash['metrics'] as $m)
                            <div><dt data-demo-metric-label>{{ $m['label'] }}</dt><dd><span data-demo-metric="{{ $m['id'] }}">{{ number_format($m['value'], 0, ',', ' ') }}</span>{{ isset($m['unit']) ? ' '.$m['unit'] : '' }}</dd></div>
                        @endforeach
                    </dl>
                    </div>
                    <h3 class="demo-col-title demo-sub">Dokumenty <small>{{ count($dash['documents']) + 1 }}</small></h3>
                    <ul class="demo-docs" data-demo-docs>
                        @foreach ($dash['documents'] as $doc)
                            <li class="demo-doc"><span class="demo-doc-title">{{ $doc['title'] }}</span><span class="demo-doc-when">{{ $doc['when'] }}</span></li>
                        @endforeach
                    </ul>
                </div>

                {{-- Kolumna 2: zadania --}}
                <div class="demo-col" data-demo-col="zadania">
                    <h3 class="demo-col-title">Zadania</h3>
                    <p class="demo-col-hint">Co robi w tej chwili i co już skończyła.</p>
                    <div class="demo-tasks" data-demo-tasks>
                        @if ($initialDecision)
                            <article class="demo-task is-decision" data-area="{{ $initialDecision['area'] }}">
                                <div class="demo-task-head"><h4>{{ $initialDecision['done'] }}</h4><span class="demo-status"><i aria-hidden="true"></i><span>do decyzji</span></span></div>
                                <p class="demo-task-outcome">{{ $initialDecision['outcome'] }}</p>
                                <div class="demo-task-foot"><span class="demo-tag">{{ $demo['areas'][$initialDecision['area']] }}</span><span class="demo-task-time">przed chwilą</span></div>
                            </article>
                        @endif
                        @foreach ($initialTasks as $i => $t)
                            <article class="demo-task is-done" data-area="{{ $t['area'] }}">
                                <div class="demo-task-head"><h4>{{ $t['done'] }}</h4><span class="demo-status"><i aria-hidden="true"></i><span>gotowe</span></span></div>
                                <p class="demo-task-outcome">{{ $t['outcome'] }}</p>
                                <div class="demo-task-foot"><span class="demo-tag">{{ $demo['areas'][$t['area']] }}</span><span class="demo-task-time">{{ $i === 0 ? '12 min temu' : '35 min temu' }}</span></div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Kolumna 3: kanały --}}
                <div class="demo-col" data-demo-col="kanaly">
                    <h3 class="demo-col-title">Kanały</h3>
                    <p class="demo-col-hint">Reklamy, posty, skrzynka i faktury w jednym miejscu.</p>
                    <div data-demo-channels>
                    <div class="demo-channel"><h5>{{ $dash['channels']['ads']['title'] }}</h5><p data-demo-channel="ads">{{ $dash['channels']['ads']['line'] }}</p></div>
                    <div class="demo-channel"><h5>{{ $dash['channels']['social']['title'] }}</h5>
                        <div class="demo-post"><span data-demo-channel="social-post">{{ $dash['channels']['social']['post'] }}</span><small>{{ $dash['channels']['social']['meta'] }}</small></div>
                    </div>
                    <div class="demo-channel"><h5>{{ $dash['channels']['inbox']['title'] }}</h5><p>{{ $dash['channels']['inbox']['line'] }}</p></div>
                    <div class="demo-channel"><h5>{{ $dash['channels']['invoices']['title'] }}</h5><p data-demo-channel="invoices">{{ $dash['channels']['invoices']['line'] }}</p></div>
                    </div>
                </div>

                {{-- Kolumna 4: dziś / decyzje --}}
                <div class="demo-col" data-demo-col="dzis">
                    <h3 class="demo-col-title">Dla Ciebie</h3>
                    <p class="demo-col-hint">Tylko decyzje i raport. Reszta dzieje się sama.</p>
                    <ul class="demo-today" data-demo-today>
                        @if ($initialDecision)
                            <li class="demo-note is-decision">
                                <p class="demo-note-text">{{ $initialDecision['done'] }}. {{ $initialDecision['outcome'] }}</p>
                                <div class="demo-note-actions"><span class="demo-btn">Zatwierdź</span><span class="demo-btn demo-btn-ghost">Odrzuć</span></div>
                                <span class="demo-note-time">czeka</span>
                            </li>
                        @endif
                        @foreach ($dash['today'] as $n)
                            <li class="demo-note"><p class="demo-note-text">{{ $n['text'] }}</p><span class="demo-note-time">{{ $n['time'] }}</span></li>
                        @endforeach
                    </ul>
                    <div class="demo-ask" data-demo-ask aria-hidden="true"><span>Zapytaj Ferajnę o cokolwiek…</span><span>↵</span></div>
                </div>
            </div>
            <script type="application/json">{!! json_encode($demo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
        </div>
        <p class="mt-3 font-mono text-xs uppercase tracking-[0.12em] text-stone">Przykład poglądowy · firma i dane fikcyjne · każda sesja wygląda inaczej</p>
    </div>

    <div class="container-x">
        <div class="mt-12 grid gap-10 lg:mt-16 lg:grid-cols-2 lg:gap-16">
            <div>
                <p class="max-w-[40ch] text-lg leading-snug text-ink-soft sm:text-xl">{{ $site['hero']['subheading'] }}</p>
                <div class="mt-7 max-w-lg">
                    <x-email-form location="hero" :site="$site" :cta="$site['hero']['cta_primary']" :note="$content->fill($site['hero']['note'])" />
                </div>
                <a href="#noc" class="btn-ghost mt-6 px-0 font-mono text-sm" data-plausible="cta_click" data-plausible-prop-location="hero_secondary">
                    {{ $site['hero']['cta_secondary'] }} ↓
                </a>
            </div>
            <dl class="rule-top self-start font-mono text-sm">
                @foreach ($site['hero']['facts'] as $fact)
                    <div class="flex items-baseline justify-between gap-6 border-b border-line py-3">
                        <dt class="text-ink">{{ $fact['label'] }}</dt>
                        <dd class="text-right text-stone">
                            @if (! empty($fact['stamp']))
                                <span class="stamp">{{ $fact['stamp'] }}</span>
                            @else
                                {{ $fact['value'] }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</section>
