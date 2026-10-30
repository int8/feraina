<section class="section" id="co">
    <div class="wrap">
        <div class="sec-head">
            <p class="eyebrow">{{ $site['features']['eyebrow'] }}</p>
            <h2 class="section-title" data-reveal>{{ $site['features']['heading'] }}</h2>
        </div>

        <div class="tiles">
            @foreach ($site['features']['items'] as $feat)
                <article class="tile">
                    <div class="tile-img" aria-hidden="true">
                        <div class="ui">
                            @if ($feat['ui'] === 'diff')
                                <div class="ui-top"><span>strona / cennik</span><span>wdrożone · odwracalne</span></div>
                                <div class="ui-body diff">
                                    <span class="ctx">nagłówek na telefonie</span>
                                    <span class="del">Kompleksowe rozwiązania językowe dla wymagających</span>
                                    <span class="add">Angielski od zera. Grupy wieczorne, 3 miejsca.</span>
                                    <span class="ctx">przycisk</span>
                                    <span class="del">Skontaktuj się z nami</span>
                                    <span class="add">Sprawdź wolne terminy</span>
                                    <span class="ctx">wynik: +12% kliknięć</span>
                                </div>
                            @elseif ($feat['ui'] === 'keywords')
                                <div class="ui-top"><span>google ads</span><span>dziś 55 zł z 80 zł</span></div>
                                <div class="ui-body kw">
                                    <div><span>angielski dla dorosłych lublin</span><span class="m">4 zapisy</span><span class="sw"></span></div>
                                    <div><span>kurs angielskiego wieczorem</span><span class="m">2 zapisy</span><span class="sw"></span></div>
                                    <div class="off"><span>darmowy angielski online</span><span class="m">0 · −40 zł</span><span class="sw"></span></div>
                                    <div><span>test poziomu angielski</span><span class="m">1 zapis</span><span class="sw"></span></div>
                                </div>
                            @elseif ($feat['ui'] === 'messenger')
                                <div class="ui-top"><span>messenger</span><span>wt 14:12</span></div>
                                <div class="ui-body msg">
                                    <div class="in">Dzień dobry, czy są jeszcze miejsca w grupie od zera?<span class="t">Marta K. · 14:12</span></div>
                                    <div class="out">Tak, zostały 3 miejsca w grupie wt–czw 18:30, start 4 listopada. Semestr 1 290 zł, można w 2 ratach.<span class="t">AI w imieniu szkoły · 14:14</span></div>
                                    <div class="in">Super, dziękuję!<span class="t">Marta K. · 14:15</span></div>
                                </div>
                            @elseif ($feat['ui'] === 'invoices')
                                <div class="ui-top"><span>faktury / październik</span><span>8 wystawionych</span></div>
                                <div class="ui-body inv">
                                    <div><span>FV/10/148 · Nowak Anna</span><span class="k">KSeF ✓</span></div>
                                    <div><span>FV/10/149 · Pracownia Lniana</span><span class="k">KSeF ✓</span></div>
                                    <div><span>FV/10/150 · Kowalski Jan</span><span class="k">14 dni po terminie</span></div>
                                    <div><span>FV/10/151 · Biuro Saldo</span><span class="k">termin 30.10</span></div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <h3>{{ $feat['title'] }}</h3>
                    <p>{{ $feat['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
