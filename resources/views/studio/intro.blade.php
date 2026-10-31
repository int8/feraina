<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1920">
    <meta name="robots" content="noindex">
    <title>Studio · intro</title>
    @vite(['resources/css/studio.css', 'resources/js/studio.js'])
</head>
<body class="studio">
{{--
    Scena 1920×1080 do nagrania filmu z hero (npm run record:intro). Ciemna, jak strona.
    Przebieg: kafelek z logo → klik → okno aplikacji → „Utwórz firmę" → opis pomysłu →
    kamera jeździ po zadaniach (zbliżenie, kroki, zwinięcie) → dziennik → klik „Opublikuj stronę"
    → konsola wdrożenia z paskiem postępu → telefon z mobilną wersją strony klienta.
    Kamera: studio.js ustawia transform na .scene (translate + scale) tak, by wybrany element był na środku kadru.
--}}
{{-- Ikony marek dla studio.js (treść zadań i dziennika buduje się w JS). JSON, nie skrypt – CSP go nie blokuje. --}}
<script type="application/json" id="brand-icons">{!! json_encode(collect(['blik', 'przelewy24', 'google-ads', 'facebook', 'instagram', 'google', 'ksef'])->mapWithKeys(fn ($id) => [$id => \App\Support\Brands::icon($id)->toHtml()])) !!}</script>

<div class="scene" id="scene">
    <div class="desk"></div>
    <div class="menubar" aria-hidden="true">
        <span class="mark"><x-logo-mark class="lamp" /></span><b>Udaina</b><span>Plik</span><span>Edycja</span><span>Widok</span><span>Okno</span><span>Pomoc</span>
        <span class="sp"></span>
        <span class="ctl"><i class="wifi"></i><i class="batt"></i><span>pon 14:32</span></span>
    </div>

    <div class="tile" id="tile">
        <div class="tile-ico"><x-logo-mark class="lamp" /></div>
        <div class="tile-label">Udaina</div>
    </div>

    <div class="win" id="win">
        <div class="win-bar">
            <span class="dots"><i></i><i></i><i></i></span>
            <span class="win-title"><x-logo-mark class="lamp" />Udaina</span>
            <span class="win-right">plan Biznes · 300 zł / mies.</span>
        </div>
        <div class="win-body">
            <div class="start" id="start">
                <x-logo-mark class="lamp" />
                <h2>Zacznijmy od Twojej firmy</h2>
                <p>Opisz pomysł po ludzku. Agenci AI zajmą się resztą.</p>
                <button type="button" class="start-btn" id="start-btn">Utwórz firmę</button>
            </div>

            <div class="panel" id="panel" hidden>
                <div class="bar">
                    <span id="p-explain">1. Ty opisujesz pomysł — bez briefu, bez specyfikacji.</span>
                    <span>wydane <b id="p-spent">0,00 zł</b></span>
                    <span class="red">do decyzji <b id="p-dec">0</b></span>
                    <button type="button" class="deploy" id="p-deploy" disabled>Opublikuj stronę</button>
                </div>

                <div class="stage" id="st-prompt">
                    <div class="ask-label">Opisz, co chcesz zbudować</div>
                    <div class="ask-box" id="ask-box"><span id="p-typed"></span><i class="caret"></i></div>
                    <div class="ask-foot"><span>Po ludzku, bez briefu.</span><span class="key" id="p-enter">↵ Enter</span></div>
                </div>

                <div class="stage cols2" id="st-work" hidden>
                    <div class="col" id="col-plan">
                        <h4>Plan</h4><p class="hint">Zadania wynikające z opisu.</p>
                        <div class="tasks" id="p-tasks"></div>
                    </div>
                    <div class="col" id="col-log">
                        <h4>Dziennik</h4><p class="hint">Każda zmiana i każdy wydatek.</p>
                        <ol class="plog" id="p-log"></ol>
                        <div class="budget"><span>Wydano w projekcie</span><span id="p-week">0 zł z 300 zł</span></div>
                        <div class="meter"><i id="p-meter"></i></div>
                        <div class="report" id="p-report" hidden><b>Raport</b><span>5 zadań · 1 do decyzji · 14 zł</span></div>
                    </div>
                </div>

                {{-- Konsola wdrożenia: pojawia się po kliknięciu „Opublikuj stronę". --}}
                <div class="stage" id="st-deploy" hidden>
                    <div class="term" id="term">
                        <div class="term-bar"><span class="dots"><i></i><i></i><i></i></span><span>udaina deploy — pomocnizdzielnicy.pl</span><span class="term-pct" id="t-pct">0%</span></div>
                        <pre class="term-out" id="t-out"></pre>
                        <div class="term-prog"><i id="t-prog"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Telefon z mobilną wersją strony klienta (po wdrożeniu). --}}
    <div class="phone" id="phone">
        <div class="phone-frame"></div>
        <div class="phone-screen">
            <div class="phone-island"></div>
            <div class="phone-status"><span>9:41</span><span class="phone-sig"><i></i><i></i><i></i></span></div>
            <div class="m" id="m">
                <header class="m-top">
                    <b><i></i>Pomocni z Dzielnicy</b>
                    <span class="m-burger"><i></i><i></i></span>
                </header>
                <div class="m-scroll" id="m-scroll">
                    <section class="m-hero">
                        <div class="m-eyebrow">Twoja dzielnica · Mokotów</div>
                        <h1>Pomoc od sąsiadów.<br>Dziś albo jutro.</h1>
                        <p>Sprzątanie, zakupy, spacer z psem, drobne naprawy. Sprawdzeni ludzie z okolicy.</p>
                        <div class="m-search"><span class="m-glass"></span><span>Czego potrzebujesz?</span><b>Szukaj</b></div>
                        <div class="m-chips"><span>🧹 Sprzątanie</span><span>🛒 Zakupy</span><span>🐕 Spacer z psem</span><span>🔧 Naprawy</span><span>👵 Towarzystwo</span></div>
                        <div class="m-stats"><div><b>42</b><span>pomocników</span></div><div><b>4,9</b><span>★ 312 opinii</span></div><div><b>20 min</b><span>czas odpowiedzi</span></div></div>
                    </section>
                    <section class="m-sec">
                        <div class="m-sec-h"><h2>Dostępni dziś</h2><a>Zobacz wszystkich</a></div>
                        <div class="m-card"><i class="av a1">AM</i><div><b>Anna M. <em>✓</em></b><span>★ 4,9 · 38 opinii</span><span>Zakupy, sprzątanie</span></div><div class="m-right"><b>35 zł/h</b><span class="ok">dziś po 16:00</span></div></div>
                        <div class="m-card"><i class="av a2">TK</i><div><b>Tomasz K. <em>✓</em></b><span>★ 4,8 · 21 opinii</span><span>Drobne naprawy, montaż</span></div><div class="m-right"><b>60 zł/h</b><span>jutro</span></div></div>
                        <div class="m-card"><i class="av a3">ZW</i><div><b>Zofia W. <em>✓</em></b><span>★ 5,0 · 12 opinii</span><span>Spacer z psem, opieka</span></div><div class="m-right"><b>30 zł/h</b><span class="ok">dziś</span></div></div>
                    </section>
                    <section class="m-sec m-how">
                        <h2>Jak to działa</h2>
                        <div><b>1</b><div><strong>Opisz, czego potrzebujesz</strong><span>Jedno zdanie wystarczy.</span></div></div>
                        <div><b>2</b><div><strong>Wybierz pomocnika</strong><span>Profil, opinie, cena z góry.</span></div></div>
                        <div><b>3</b><div><strong>Zapłać po wykonaniu</strong><span>Pieniądze trafiają do pomocnika, gdy potwierdzisz.</span></div></div>
                    </section>
                    <section class="m-trust">
                        <div><span>Bezpieczna płatność</span><b><x-brand id="blik" /> BLIK <x-brand id="przelewy24" /> Przelewy24</b></div>
                        <div><span>Każdy pomocnik</span><b>🛡 zweryfikowany dowodem</b></div>
                    </section>
                    <footer class="m-foot">Pomocni z Dzielnicy · pomocnizdzielnicy.pl · 2026</footer>
                </div>
                <div class="m-cta"><b>Zamów pomoc</b><span>Zostań pomocnikiem</span></div>
            </div>
        </div>
        <div class="toast" id="toast">Opublikowano · pomocnizdzielnicy.pl</div>
    </div>

    <svg class="cursor" id="cursor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 2.5l15.5 9.3-7 1.6-3.7 6.4z"/></svg>
</div>
</body>
</html>
