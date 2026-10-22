<section class="hero center" id="top">
    <div class="wrap">
        <h1 class="display">{!! nl2br(e($site['hero']['heading'])) !!}</h1>

        <div class="hero-cta">
            <a href="#zapis" class="btn-stamp px-7 py-4 text-lg" data-open-beta data-plausible="cta_click" data-plausible-prop-location="hero">{{ $site['hero']['cta_primary'] }}</a>
        </div>

        {{-- Demo: jedno okno Chrome, dwie karty (Udaina i gotowa strona klienta) --}}
        <div class="bw-stage" id="bw-stage">
            <div class="panel" id="panel" aria-label="Przykładowy przebieg pracy Udainy w przeglądarce">
                <x-bw-chrome
                    :tabs="[
                        ['id' => 'udaina', 'title' => 'Udaina', 'sub' => 'etap: opis pomysłu', 'subId' => 'p-step', 'active' => true],
                        ['id' => 'site', 'title' => 'Pomocni z Dzielnicy', 'fav' => 'dot', 'hidden' => true],
                    ]"
                    host="app.udaina.pl" host-id="p-host" path="/projekty/nowy" path-id="p-url" />

                {{-- Karta 1: Udaina --}}
                <div class="bw-page" data-page="udaina">
                    <svg class="p-cursor" id="p-cursor" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3l14 9-6.2 1.2 3.4 6.4-2.6 1.3-3.4-6.4L5 19z"/></svg>
                    <div class="bar">
                        <span>wydane <b id="p-spent">0,00 zł</b></span>
                        <span>limit <b>300 zł</b></span>
                        <span class="red">do decyzji <b id="p-dec">0</b></span>
                        <span>dziennik: <b>wszystko odwracalne</b></span>
                    </div>
                    <div class="bw-view" id="p-view">

                        {{-- stage 1: prompt --}}
                        <div class="stage" id="st-prompt">
                            <div class="ask-label">Opisz, co chcesz zbudować</div>
                            <div class="ask-box"><span id="p-typed"></span><i class="caret"></i></div>
                            <div class="ask-foot"><span>Po ludzku, bez briefu.</span><span class="key" id="p-enter">↵ Enter</span></div>
                        </div>

                        {{-- stage 1b: krótkie ładowanie po Enterze --}}
                        <div class="stage" id="st-load" hidden>
                            <div class="load"><i class="load-dot"></i><span>Analizuję opis</span><div class="load-bar"><i id="p-load-bar"></i></div></div>
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

                        {{-- stage 3: report + decisions (the site itself lives in the second tab) --}}
                        <div class="stage" id="st-result" hidden>
                            <div class="result-head">
                                <div><h4>Gotowe do publikacji</h4><p class="hint">Strona stoi na pomocnizdzielnicy.pl, w sąsiedniej karcie. Profile, płatności i regulamin w dzienniku.</p></div>
                                <div class="acts"><span class="chip">Opublikuj stronę</span><span class="chip ghost">Popraw</span></div>
                            </div>
                            <ul class="sum">
                                <li><b>7</b><span>zadań wykonanych</span></li>
                                <li><b>1</b><span>decyzja do podjęcia: kampania Google Ads, 40 zł dziennie</span></li>
                                <li><b>14 zł</b><span>wydane z limitu 300 zł (domena)</span></li>
                                <li><b>18:00</b><span>post na grupie dzielnicy, zaplanowany</span></li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Karta 2: strona klienta, najpierw w trakcie wdrożenia, potem gotowa --}}
                <div class="bw-page" data-page="site" hidden>
                    <div class="bw-view bw-view-site" id="p-site-view">
                        <div class="cook" id="p-cook">
                            <div class="cook-head">
                                <svg class="cook-flame" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2c1 4 5 5.5 5 10.5A5 5 0 0 1 7 12.5c0-1.5.5-2.5 1-3.5.5 2 1.5 2.5 2.5 2.5C10 8 10 5 12 2z"/></svg>
                                <span>Wdrażam stronę</span>
                            </div>
                            <div class="cook-sub">pomocnizdzielnicy.pl · wersja 1 · publikacja w toku, nie wymaga Twojej uwagi</div>
                            <div class="cook-bar"><i id="p-cook-bar"></i></div>
                            <ol class="cook-steps" id="p-cook-steps"></ol>
                        </div>
                        <div class="site" hidden>
                            <header class="s-nav">
                                <b class="s-logo"><i></i>Pomocni z Dzielnicy</b>
                                <nav><span>Jak to działa</span><span>Pomocnicy</span><span>Cennik</span><span>Zostań pomocnikiem</span></nav>
                                <div class="s-nav-acts"><span class="s-link">Zaloguj się</span><span class="s-btn">Zamów pomoc</span></div>
                            </header>

                            <section class="s-hero">
                                <i class="s-glow s-glow-a"></i><i class="s-glow s-glow-b"></i>
                                <div class="s-hero-copy">
                                    <span class="s-pill"><i></i>42 pomocników dostępnych dziś · Stare Bielany</span>
                                    <h5>Pomoc od sąsiadów.<br><em>Dziś albo jutro.</em></h5>
                                    <p>Sprzątanie, zakupy, spacer z psem, drobne naprawy. Sprawdzeni ludzie z Twojej dzielnicy. Płacisz dopiero po wykonaniu.</p>
                                    <div class="s-search">
                                        <span class="s-field"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="6"/><path d="m20 20-4.5-4.5"/></svg><span>Czego potrzebujesz? np. zakupy w czwartek</span></span>
                                        <span class="s-field s-field-sm"><svg viewBox="0 0 24 24"><rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 10h16M8 3v4M16 3v4"/></svg><span>Kiedy</span></span>
                                        <span class="s-btn">Znajdź pomoc</span>
                                    </div>
                                    <div class="s-tags"><span>Sprzątanie</span><span>Zakupy</span><span>Spacer z psem</span><span>Drobne naprawy</span><span>Opieka</span></div>
                                </div>
                                <div class="s-hero-visual">
                                    <div class="s-map">
                                        <i class="s-pin" style="--x:22%;--y:30%"></i>
                                        <i class="s-pin" style="--x:68%;--y:22%"></i>
                                        <i class="s-pin" style="--x:48%;--y:62%"></i>
                                        <i class="s-pin s-pin-me" style="--x:38%;--y:44%"></i>
                                        <div class="s-map-card"><i>AM</i><div><b>Anna M.</b><span>350 m od Ciebie · dziś 16:00</span></div><em>4,9 ★</em></div>
                                    </div>
                                    <div class="s-stats">
                                        <div><b>4,9</b><span>średnia z 312 opinii</span></div>
                                        <div><b>20 min</b><span>średni czas odpowiedzi</span></div>
                                        <div><b>100%</b><span>pomocników z weryfikacją</span></div>
                                    </div>
                                </div>
                            </section>

                            <section class="s-section">
                                <div class="s-head"><h6>W czym pomagamy</h6><span>Stawki ustalają pomocnicy. Widzisz je przed zamówieniem.</span></div>
                                <div class="s-cats">
                                    <div><i><svg viewBox="0 0 24 24"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8z"/></svg></i><b>Sprzątanie</b><span>od 35 zł/h</span></div>
                                    <div><i><svg viewBox="0 0 24 24"><path d="M3 4h2l2.4 11h11l2-7H7"/><circle cx="9.5" cy="19.5" r="1.3"/><circle cx="17.5" cy="19.5" r="1.3"/></svg></i><b>Zakupy</b><span>od 30 zł</span></div>
                                    <div><i><svg viewBox="0 0 24 24"><circle cx="7" cy="8" r="1.8"/><circle cx="12" cy="6" r="1.8"/><circle cx="17" cy="8" r="1.8"/><path d="M12 11c-3 0-5.5 2.6-5.5 5.2a2.2 2.2 0 0 0 3.3 1.9c1.4-.8 3-.8 4.4 0a2.2 2.2 0 0 0 3.3-1.9C17.5 13.6 15 11 12 11z"/></svg></i><b>Spacer z psem</b><span>od 30 zł</span></div>
                                    <div><i><svg viewBox="0 0 24 24"><path d="M14.7 6.3a4 4 0 0 0-5.4 5.1L4 16.7 7.3 20l5.3-5.3a4 4 0 0 0 5.1-5.4l-2.4 2.4-2.8-2.8z"/></svg></i><b>Drobne naprawy</b><span>od 60 zł/h</span></div>
                                    <div><i><svg viewBox="0 0 24 24"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/></svg></i><b>Opieka</b><span>od 40 zł/h</span></div>
                                    <div><i><svg viewBox="0 0 24 24"><path d="M5 19c0-8 5-13 14-14-1 9-6 14-14 14z"/><path d="M5 19l7-7"/></svg></i><b>Ogród</b><span>od 45 zł/h</span></div>
                                </div>
                            </section>

                            <section class="s-section">
                                <div class="s-head"><h6>Pomocnicy w Twojej okolicy</h6><span class="s-link">Zobacz wszystkich (42) →</span></div>
                                <div class="s-helpers">
                                    <div class="s-card">
                                        <div class="s-card-top"><i class="s-ava a">AM</i><div><b>Anna M. <svg viewBox="0 0 24 24"><path d="m5 12 5 5L20 7"/></svg></b><span>★ 4,9 · 38 opinii · 3 lata na portalu</span></div></div>
                                        <div class="s-chips"><span>Zakupy</span><span>Sprzątanie</span><span>Gotowanie</span></div>
                                        <div class="s-card-foot"><b>od 35 zł/h</b><em><i></i>dziś po 16:00</em><span class="s-btn s-btn-sm">Zamów</span></div>
                                    </div>
                                    <div class="s-card">
                                        <div class="s-card-top"><i class="s-ava b">TK</i><div><b>Tomasz K. <svg viewBox="0 0 24 24"><path d="m5 12 5 5L20 7"/></svg></b><span>★ 4,8 · 21 opinii · elektryk z uprawnieniami</span></div></div>
                                        <div class="s-chips"><span>Drobne naprawy</span><span>Montaż</span><span>Elektryka</span></div>
                                        <div class="s-card-foot"><b>od 60 zł/h</b><em><i></i>jutro od 9:00</em><span class="s-btn s-btn-sm">Zamów</span></div>
                                    </div>
                                    <div class="s-card">
                                        <div class="s-card-top"><i class="s-ava c">ZW</i><div><b>Zofia W. <svg viewBox="0 0 24 24"><path d="m5 12 5 5L20 7"/></svg></b><span>★ 5,0 · 12 opinii · studentka pielęgniarstwa</span></div></div>
                                        <div class="s-chips"><span>Spacer z psem</span><span>Opieka</span><span>Zakupy</span></div>
                                        <div class="s-card-foot"><b>od 30 zł</b><em><i></i>dziś</em><span class="s-btn s-btn-sm">Zamów</span></div>
                                    </div>
                                </div>
                            </section>

                            <section class="s-section s-steps">
                                <div><b>1</b><h6>Opisz, czego potrzebujesz</h6><span>Po ludzku, w jednym zdaniu. Dodaj termin, resztę ogarniemy.</span></div>
                                <div><b>2</b><h6>Wybierz pomocnika</h6><span>Po opiniach sąsiadów, stawce i dostępności. Każdy zweryfikowany dowodem.</span></div>
                                <div><b>3</b><h6>Zapłać po wykonaniu</h6><span>BLIK albo Przelewy24. Pieniądze trafiają do pomocnika po Twoim potwierdzeniu.</span></div>
                            </section>

                            <section class="s-section s-quote">
                                <i class="s-ava d">HB</i>
                                <blockquote>„Mama ma 84 lata i nie lubi obcych. Pani Ania przychodzi od pół roku z zakupami i już jest «swoja». Płacę przez portal, więc nie ma rozmów o pieniądzach."</blockquote>
                                <span>Hanna B., Stare Bielany · <em>★★★★★</em></span>
                            </section>

                            <footer class="s-foot">
                                <div class="s-foot-pay"><span>Płatności</span><b>BLIK</b><b>Przelewy24</b><b>Visa</b><b>Mastercard</b></div>
                                <span>Każdy pomocnik zweryfikowany dowodem osobistym · Ubezpieczenie OC do 20 000 zł</span>
                                <nav><span>Regulamin</span><span>Prywatność</span><span>Kontakt</span></nav>
                            </footer>
                        </div>
                    </div>
                </div>
            </div>
            <canvas class="fireworks" id="p-fireworks" aria-hidden="true"></canvas>
        </div>
        <div class="caption"><span>Przykład · projekt i dane fikcyjne</span><span>Karty w oknie można przełączać</span></div>
    </div>
</section>
