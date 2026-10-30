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
    Scena 1920×1080 do nagrania filmu z hero (npm run record:intro).
    Przebieg: kafelek z logo → klik → okno aplikacji → „Utwórz firmę" → opis pomysłu →
    kamera jeździ po zadaniach (zoom, kroki, zoom out) → dziennik → klik „Opublikuj stronę" → okno przeglądarki ze stroną.
    Kamera: studio.js ustawia transform na .scene (translate + scale) tak, by wybrany element był na środku kadru.
--}}
<div class="scene" id="scene">
    <div class="desk"></div>

    <div class="tile" id="tile">
        <div class="tile-ico"><x-logo-mark class="" /></div>
        <div class="tile-label">Biznes w 5 minut</div>
    </div>

    <div class="win" id="win">
        <div class="win-bar">
            <span class="dots"><i></i><i></i><i></i></span>
            <span class="win-title"><x-logo-mark class="" />Biznes w 5 minut</span>
            <span class="win-right">plan Biznes · 300 zł / mies.</span>
        </div>
        <div class="win-body">
            <div class="start" id="start">
                <x-logo-mark class="" />
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
            </div>
        </div>
    </div>

    {{-- Okno przeglądarki z gotową stroną klienta (pojawia się po kliknięciu „Opublikuj stronę"). --}}
    <div class="browser" id="browser">
        <div class="browser-bar">
            <span class="dots"><i></i><i></i><i></i></span>
            <span class="url"><i class="lock"></i>pomocnizdzielnicy.pl</span>
            <span class="browser-right">★</span>
        </div>
        <div class="browser-body">
            <div class="site" id="site">
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
                <div class="site-cards">
                    <div><i>AM</i><div><b>Anna M.</b><span>★ 4,9 · 38 opinii</span><span>Zakupy, sprzątanie · od 35 zł/h</span><em>dziś po 16:00</em></div></div>
                    <div><i>TK</i><div><b>Tomasz K.</b><span>★ 4,8 · 21 opinii</span><span>Drobne naprawy · od 60 zł/h</span><em>jutro</em></div></div>
                    <div><i>ZW</i><div><b>Zofia W.</b><span>★ 5,0 · 12 opinii</span><span>Spacer z psem, opieka · od 30 zł/h</span><em>dziś</em></div></div>
                </div>
                <div class="site-foot"><span>Płatność BLIK lub Przelewy24 · pieniądze trafiają do pomocnika po potwierdzeniu</span><span>Każdy pomocnik zweryfikowany dowodem</span></div>
            </div>
        </div>
        <div class="toast" id="toast">Strona opublikowana · pomocnizdzielnicy.pl</div>
    </div>

    <svg class="cursor" id="cursor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 2.5l15.5 9.3-7 1.6-3.7 6.4z"/></svg>
</div>
</body>
</html>
