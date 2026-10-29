<section class="hero center" id="top">
    <div class="wrap">
        {{-- *słowo* w treści = akcent w kolorze stempla --}}
        @php($accent = fn (string $t) => preg_replace('/\*(.+?)\*/u', '<span class="accent">$1</span>', nl2br(e($t))))
        <h1 class="display" style="margin-top: 48px;">{!! $accent($site['hero']['heading']) !!}</h1>
        <div class="hero-sub">
            <p class="hero-sub-lead">{!! $accent($site['hero']['sub_lead']) !!}</p>
        </div>

        {{-- Panel produktowy --}}
        <div class="panel" id="panel" aria-label="Przykładowy przebieg pracy Udainy">
            <div class="chrome"><span class="dots"><i></i><i></i><i></i></span></div>
            <div class="bar">
                <span id="p-explain">1. Ty opisujesz pomysł — bez briefu, bez specyfikacji.</span>
                <span>wydane <b id="p-spent">0,00 zł</b></span>
                <span class="red">do decyzji <b id="p-dec">0</b></span>
            </div>

            {{-- stage 1: prompt --}}
            <div class="stage" id="st-prompt">
                <div class="ask-label">Opisz, co chcesz zbudować</div>
                <div class="ask-box"><span id="p-typed"></span><i class="caret"></i></div>
                <div class="ask-foot"><span>Po ludzku, bez briefu.</span><span class="key" id="p-enter">↵ Enter</span></div>
            </div>

            {{-- stage 2: plan + log --}}
            <div class="stage cols2" id="st-work" hidden>
                <div class="col">
                    <h4>Plan</h4><p class="hint">Zadania wynikające z opisu.</p>
                    <div class="tasks" id="p-tasks"></div>
                </div>
                <div class="col">
                    <h4>Dziennik</h4><p class="hint">Co dzieje się w tej chwili.</p>
                    <ol class="plog" id="p-log"></ol>
                    <div class="budget"><span>Wydano w projekcie</span><span id="p-week">0 zł z 300 zł</span></div>
                    <div class="meter"><i id="p-meter"></i></div>
                </div>
            </div>

            {{-- stage 3: result --}}
            <div class="stage" id="st-result" hidden>
                <div class="result-head">
                    <div><h4>Gotowe do publikacji</h4><p class="hint">Strona, profile pomocników, płatności i regulamin. Wszystko w dzienniku.</p></div>
                    <div class="acts" id="p-pub"><button class="chip" type="button" data-ok>Opublikuj stronę</button><button class="chip ghost" type="button" data-no>Popraw</button></div>
                </div>
                <div class="site">
                    <div class="site-nav"><b><i></i>Pomocni z Dzielnicy</b><span>Jak to działa · Pomocnicy · Cennik · Zostań pomocnikiem</span><span class="site-btn">Zamów pomoc</span></div>
                    <div class="site-hero">
                        <div>
                            <div class="site-eyebrow">Dla seniorów i rodziców z małymi dziećmi</div>
                            <h5>Pomoc od sąsiadów. Dziś albo jutro.</h5>
                            <p>Sprzątanie, zakupy, spacer z psem, drobne naprawy. Sprawdzeni pomocnicy z Twojej dzielnicy, płatność bezpiecznie przez portal.</p>
                            <div class="site-search"><span>Czego potrzebujesz? np. zakupy w czwartek</span><span class="site-btn">Znajdź pomoc</span></div>
                            <div class="site-tags"><span>🧹 Sprzątanie</span><span>🛒 Zakupy</span><span>🐕 Spacer z psem</span><span>🔧 Drobne naprawy</span></div>
                        </div>
                        <div class="site-proof">
                            <div class="site-avatars"><i>AM</i><i>TK</i><i>ZW</i><i>PN</i><i>+38</i></div>
                            <b>42 pomocników w dzielnicy</b>
                            <span>★ 4,9 średnia z 312 opinii</span>
                            <span>Odpowiedź zwykle w 20 minut</span>
                        </div>
                    </div>
                    <div class="site-steps">
                        <div><b>1</b><span>Opisz, czego potrzebujesz</span></div>
                        <div><b>2</b><span>Wybierz pomocnika po opiniach</span></div>
                        <div><b>3</b><span>Zapłać BLIK-iem po wykonaniu</span></div>
                    </div>
                    <div class="site-cards">
                        <div><i>AM</i><div><b>Anna M.</b><span>★ 4,9 · 38 opinii</span><span>Zakupy, sprzątanie · od 35 zł/h</span><em>dziś po 16:00</em></div></div>
                        <div><i>TK</i><div><b>Tomasz K.</b><span>★ 4,8 · 21 opinii</span><span>Drobne naprawy · od 60 zł/h</span><em>jutro</em></div></div>
                        <div><i>ZW</i><div><b>Zofia W.</b><span>★ 5,0 · 12 opinii</span><span>Spacer z psem, opieka · od 30 zł/h</span><em>dziś</em></div></div>
                    </div>
                    <div class="site-foot"><span>Płatność BLIK lub Przelewy24 · pieniądze trafiają do pomocnika po potwierdzeniu</span><span>Każdy pomocnik zweryfikowany dowodem</span></div>
                </div>
            </div>
        </div>
        <div class="caption"><span>Przykład · projekt i dane fikcyjne</span><button class="chip ghost" type="button" id="p-replay">↻ Odtwórz ponownie</button></div>
    </div>
</section>
