/**
 * Panel produktowy: animacja przebiegu pracy Udainy.
 * Etapy: prompt → praca (plan + dziennik) → gotowa strona.
 */

const PROMPT = 'Chcę zrobić portal dla mojej dzielnicy, gdzie seniorzy i rodzice z małymi dziećmi znajdą pomoc: sprzątanie, zakupy, wyprowadzenie psa, drobne naprawy. Pomocnicy mają profile z opiniami, a płatność idzie przez portal.';

const TASKS = [
    ['Research', 'Analiza podobnych portali', 'Sprawdzono 6 serwisów sąsiedzkich w Polsce. Luka: brak oferty dla seniorów i rodziców w jednej dzielnicy.', 0],
    ['Strona', 'Nazwa i strona główna', '„Pomocni z Dzielnicy". Nagłówek, wyszukiwarka usług, 6 kategorii. Teksty bez żargonu.', 0],
    ['Profile', 'Profile pomocników z opiniami', 'Zdjęcie, zakres usług, stawka, oceny po każdej usłudze. Weryfikacja tożsamości przed pierwszym zleceniem.', 0],
    ['Płatności', 'Płatność przez portal', 'BLIK i Przelewy24. Pieniądze trafiają do pomocnika po potwierdzeniu wykonania.', 0],
    ['Prawo', 'Regulamin i RODO', 'Regulamin portalu, polityka prywatności, zgody przy rejestracji. Do przejrzenia przez Ciebie.', 0],
    ['Marketing', 'Post na grupie dzielnicy', 'Zaplanowany na 18:00. Treść: dla kogo, jakie usługi, jak zamówić.', 0],
    ['Reklamy', 'Kampania Google Ads do akceptacji', '40 zł dziennie na „pomoc seniorom [dzielnica]". Wymaga Twojej zgody.', 1]
];

const LOG = [
    ['0:00', 'analizuję opis: 4 usługi, 2 grupy odbiorców, profile, płatności'],
    ['0:04', '6 podobnych portali przejrzanych · notatka w dzienniku'],
    ['0:11', 'domena pomocnizdzielnicy.pl wolna · rezerwuję'],
    ['0:19', 'strona główna: wersja 1 wdrożona · 14 zł (domena)'],
    ['0:27', 'profile pomocników · formularz i oceny · wdrożone'],
    ['0:34', 'Przelewy24 i BLIK podpięte w trybie testowym'],
    ['0:41', 'regulamin i polityka prywatności · szkic do Twojej akceptacji'],
    ['0:47', 'post na grupie dzielnicy zaplanowany 18:00'],
    ['0:52', 'kampania Google Ads · czeka na decyzję'],
    ['0:55', 'raport gotowy · 7 zadań, 1 do decyzji']
];

const COOK = [
    'Domena pomocnizdzielnicy.pl zarezerwowana',
    'Strona główna i wyszukiwarka usług',
    'Profile pomocników z opiniami',
    'Płatności BLIK i Przelewy24 (tryb testowy)',
    'Regulamin i polityka prywatności',
    'Certyfikat SSL · publikacja wersji 1'
];

const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initPanel() {
    const panel = document.getElementById('panel');
    if (!panel) return;

    const $ = id => document.getElementById(id);
    const stages = ['st-prompt', 'st-load', 'st-work', 'st-result'];
    let timers = [];

    const after = (ms, fn) => timers.push(setTimeout(fn, reduce ? 0 : ms));

    const URLS = { 'st-prompt': '/projekty/nowy', 'st-load': '/projekty/nowy', 'st-work': '/projekty/pomocni-z-dzielnicy', 'st-result': '/projekty/pomocni-z-dzielnicy/raport' };

    function show(id) {
        stage = id;
        stages.forEach(s => $(s).hidden = s !== id);
        if ($('p-host').textContent === HOSTS.udaina) $('p-url').textContent = URLS[id];
        const view = $('p-view');
        if (view) view.scrollTop = 0;
    }

    // Nowy wpis w kolumnie: przewiń kolumnę (albo całe okno na wąskim ekranie) do końca.
    function follow(el) {
        const col = el.closest('.col');
        const view = $('p-view');
        const target = col && col.scrollHeight > col.clientHeight ? col : view;
        if (!target) return;
        target.scrollTo({ top: target.scrollHeight, behavior: reduce ? 'auto' : 'smooth' });
    }

    function reset() {
        timers.forEach(clearTimeout);
        timers = [];
        closeTab();
        cursorReset();
        const loadBar = $('p-load-bar');
        loadBar.style.transition = 'none';
        loadBar.style.width = '0';
        $('p-typed').textContent = '';
        $('p-enter').classList.remove('hit');
        $('p-tasks').innerHTML = '';
        $('p-log').innerHTML = '';
        $('p-spent').textContent = '0,00 zł';
        $('p-dec').textContent = '0';
        $('p-week').textContent = '0 zł z 300 zł';
        $('p-meter').style.width = '0';
        $('p-step').textContent = 'etap: opis pomysłu';
        show('st-prompt');
    }

    // ----- Kursor myszy: od końca wpisanego tekstu do klawisza Enter -----
    const cursor = $('p-cursor');

    function cursorPlace(el, dx = 0, dy = 0) {
        if (!cursor || !el) return;
        const base = cursor.parentElement.getBoundingClientRect();
        const r = el.getBoundingClientRect();
        cursor.style.left = `${r.left - base.left + r.width / 2 + dx}px`;
        cursor.style.top = `${r.top - base.top + r.height / 2 + dy}px`;
    }

    function cursorReset() {
        if (!cursor) return;
        cursor.classList.remove('show', 'move', 'click');
    }

    // ----- Karty: Udaina i strona klienta -----
    const HOSTS = { udaina: 'app.udaina.pl', site: 'pomocnizdzielnicy.pl' };
    const tabs = [...panel.querySelectorAll('.bw-tab')];
    const pages = [...panel.querySelectorAll('.bw-page')];
    const siteTab = panel.querySelector('.bw-tab[data-tab="site"]');
    const siteFav = siteTab ? siteTab.querySelector('[data-fav]') : null;
    const site = panel.querySelector('.site');
    const fireworks = createFireworks($('p-fireworks'), $('bw-stage'));
    let stage = 'st-prompt';

    function activate(id) {
        tabs.forEach(t => {
            const on = t.dataset.tab === id;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', String(on));
        });
        pages.forEach(p => p.hidden = p.dataset.page !== id);
        $('p-host').textContent = HOSTS[id];
        $('p-url').textContent = id === 'udaina' ? URLS[stage] : '';
    }
    tabs.forEach(t => t.addEventListener('click', () => activate(t.dataset.tab)));

    function openTab() {
        if (!siteTab) return;
        siteTab.classList.add('new');
        siteTab.hidden = false;
        if (siteFav) siteFav.classList.add('loading');
        requestAnimationFrame(() => requestAnimationFrame(() => siteTab.classList.remove('new')));
    }

    function closeTab() {
        if (siteTab) { siteTab.hidden = true; siteTab.classList.remove('new'); }
        if (siteFav) siteFav.classList.remove('loading');
        $('p-cook').hidden = false;
        $('p-cook-steps').innerHTML = '';
        const bar = $('p-cook-bar');
        bar.style.transition = 'none';
        bar.style.width = '0';
        if (site) { site.hidden = true; site.classList.remove('in'); }
        fireworks.stop();
        activate('udaina');
    }

    // Wdrożenie: pasek postępu i lista kroków, odhaczane po kolei.
    function bake(cookMs) {
        const bar = $('p-cook-bar');
        requestAnimationFrame(() => {
            bar.style.transition = reduce ? 'none' : `width ${cookMs}ms linear`;
            bar.style.width = '100%';
        });
        const gap = cookMs / (COOK.length + 0.5);
        COOK.forEach((text, i) => {
            after(100 + i * gap, () => {
                const li = document.createElement('li');
                li.textContent = text;
                $('p-cook-steps').appendChild(li);
                requestAnimationFrame(() => li.classList.add('in'));
                after(gap * 0.8, () => li.classList.add('done'));
            });
        });
    }

    function bakeDone() {
        $('p-cook').hidden = true;
        if (siteFav) siteFav.classList.remove('loading');
        if (site) {
            site.hidden = false;
            requestAnimationFrame(() => site.classList.add('in'));
        }
        if (!reduce) fireworks.start(4200);
    }

    function play() {
        reset();

        // Stage 1: typing
        if (reduce) $('p-typed').textContent = PROMPT;
        const typed = reduce ? 0 : typeHuman(PROMPT, $('p-typed'), after);

        // Kursor: pojawia się przy karecie, jedzie na Enter, klika.
        after(typed + 300, () => {
            cursorPlace(document.querySelector('#st-prompt .caret'), 18, 14);
            cursor?.classList.add('show');
        });
        after(typed + 500, () => {
            cursor?.classList.add('move');
            cursorPlace($('p-enter'), 2, 4);
        });
        after(typed + 1300, () => {
            cursor?.classList.add('click');
            $('p-enter').classList.add('hit');
        });
        after(typed + 1450, () => cursor?.classList.remove('click'));

        // Krótkie ładowanie, potem plan i dziennik.
        after(typed + 1650, () => {
            cursorReset();
            show('st-load');
            $('p-step').textContent = 'etap: analiza opisu';
            const bar = $('p-load-bar');
            requestAnimationFrame(() => {
                bar.style.transition = reduce ? 'none' : 'width 0.55s linear';
                bar.style.width = '100%';
            });
        });
        after(typed + 2300, () => {
            show('st-work');
            $('p-step').textContent = 'etap: plan i wdrożenie';
            runWork();
        });
    }

    function runWork() {
        const step = 1100;
        const tabAt = 300 + step;                         // gdy rusza „Nazwa i strona główna": nowa karta ładuje się w tle
        const doneAt = 600 + TASKS.length * step + 900;   // koniec pracy: przechodzimy na kartę strony
        const cookMs = 3600;

        after(tabAt, openTab);

        TASKS.forEach(([area, title, desc, ask], i) => {
            after(300 + i * step, () => {
                const el = document.createElement('div');
                el.className = 'task run';
                el.innerHTML = `<div class="h"><span>${title}</span><span class="st">w toku</span></div><p>${desc}</p><div class="area">${area}</div>`;
                $('p-tasks').appendChild(el);
                requestAnimationFrame(() => { el.classList.add('in'); follow(el); });

                after(step - 150, () => {
                    el.classList.remove('run');
                    if (ask) {
                        el.classList.add('ask');
                        el.querySelector('.st').textContent = 'do decyzji';
                        $('p-dec').textContent = '1';
                    } else {
                        el.querySelector('.st').textContent = 'gotowe';
                    }
                });
            });
        });

        LOG.forEach(([time, text], i) => {
            after(200 + i * (step * TASKS.length / LOG.length), () => {
                const li = document.createElement('li');
                li.innerHTML = `<time>${time}</time><span>${text}</span>`;
                $('p-log').appendChild(li);
                requestAnimationFrame(() => { li.classList.add('in'); follow(li); });

                if (i === 3) {
                    $('p-spent').textContent = '14,00 zł';
                    $('p-week').textContent = '14 zł z 300 zł';
                    $('p-meter').style.width = '5%';
                }
            });
        });

        after(doneAt, () => {
            $('p-step').textContent = 'etap: publikacja';
            activate('site');
            bake(cookMs);
        });
        after(doneAt + cookMs + 200, bakeDone);
        after(doneAt + cookMs + 200 + 5200, () => {
            show('st-result');
            $('p-step').textContent = 'etap: raport i decyzje';
            activate('udaina');
        });
    }

    // Start animation when panel is visible
    if (reduce || !('IntersectionObserver' in window)) {
        play();
    } else {
        const observer = new IntersectionObserver(entries => {
            if (entries.some(e => e.isIntersecting)) {
                play();
                observer.disconnect();
            }
        }, { threshold: 0.25 });
        observer.observe(panel);
    }
}

/**
 * Pisanie „po ludzku": nierówne tempo, przystanki po przecinkach,
 * kilka literówek poprawionych backspace'em. Zwraca łączny czas w ms.
 */
const TYPOS = {
    'seniorzy': 'seniozr',
    'sprzątanie': 'sprzat',
    'naprawy': 'naprw',
};

function typeHuman(text, el, after) {
    const steps = []; // { add: 'x' } | { del: 1 }
    let i = 0;
    while (i < text.length) {
        const word = Object.keys(TYPOS).find(w => text.startsWith(w, i) && (i === 0 || text[i - 1] === ' '));
        if (word) {
            const wrong = TYPOS[word];
            // wspólny prefiks piszemy normalnie, potem błędną końcówkę, pauza, backspace, poprawna końcówka
            let k = 0;
            while (k < word.length && word[k] === wrong[k]) k++;
            for (const ch of word.slice(0, k)) steps.push({ add: ch });
            for (const ch of wrong.slice(k)) steps.push({ add: ch });
            steps.push({ pause: 420 });
            for (let d = 0; d < wrong.length - k; d++) steps.push({ del: 1 });
            steps.push({ pause: 120 });
            for (const ch of word.slice(k)) steps.push({ add: ch });
            i += word.length;
            continue;
        }
        steps.push({ add: text[i] });
        i++;
    }

    let t = 0;
    let out = '';
    let seed = 7;
    const rnd = () => { seed = (seed * 9301 + 49297) % 233280; return seed / 233280; };
    steps.forEach(s => {
        if (s.pause) { t += s.pause; return; }
        if (s.del) {
            t += 70;
            after(t, () => { out = out.slice(0, -1); el.textContent = out; });
            return;
        }
        let delay = 18 + rnd() * 26;
        if (s.add === ' ') delay += 20;
        if (',.:'.includes(s.add)) delay += 40;
        t += delay;
        const ch = s.add;
        after(t, () => { out += ch; el.textContent = out; });
        if (',.:'.includes(ch)) t += 140;
    });
    return t;
}

/**
 * Fajerwerki na canvasie nad sceną. Kilka rakiet, wybuch cząstek, grawitacja, gaśnięcie.
 */
function createFireworks(canvas, stage) {
    if (!canvas || !stage || !canvas.getContext) return { start() {}, stop() {} };
    const ctx = canvas.getContext('2d');
    const COLORS = ['#d2232a', '#ff5a5f', '#f5b400', '#ffd84d', '#1f6f5a', '#3cb48c'];
    let rockets = [], sparks = [], raf = 0, endAt = 0, lastLaunch = 0, dpr = 1;

    function size() {
        dpr = Math.min(window.devicePixelRatio || 1, 2);
        const r = canvas.getBoundingClientRect();
        canvas.width = r.width * dpr;
        canvas.height = r.height * dpr;
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        return r;
    }

    function launch(w, h) {
        rockets.push({
            x: w * (0.15 + Math.random() * 0.7), y: h,
            vx: (Math.random() - 0.5) * 1.2, vy: -(9 + Math.random() * 4),
            fuse: h * (0.25 + Math.random() * 0.3),
            color: COLORS[Math.floor(Math.random() * COLORS.length)]
        });
    }

    function burst(r) {
        const n = 90 + Math.floor(Math.random() * 50);
        for (let i = 0; i < n; i++) {
            const a = (Math.PI * 2 * i) / n + Math.random() * 0.1;
            const v = 2.5 + Math.random() * 5;
            sparks.push({ x: r.x, y: r.y, vx: Math.cos(a) * v, vy: Math.sin(a) * v, life: 1, decay: 0.009 + Math.random() * 0.01, size: 2.5 + Math.random() * 2, color: Math.random() < 0.8 ? r.color : '#ffffff' });
        }
    }

    function frame(now) {
        const w = canvas.width / dpr, h = canvas.height / dpr;
        ctx.clearRect(0, 0, w, h);
        if (now < endAt && now - lastLaunch > 330) { launch(w, h); if (Math.random() < 0.4) launch(w, h); lastLaunch = now; }

        rockets = rockets.filter(r => {
            r.x += r.vx; r.y += r.vy; r.vy += 0.16;
            ctx.fillStyle = r.color;
            ctx.fillRect(r.x - 1.5, r.y - 1.5, 3, 3);
            if (r.y <= r.fuse || r.vy >= 0) { burst(r); return false; }
            return true;
        });
        sparks = sparks.filter(s => {
            s.x += s.vx; s.y += s.vy; s.vy += 0.07; s.vx *= 0.985; s.vy *= 0.985; s.life -= s.decay;
            if (s.life <= 0) return false;
            ctx.globalAlpha = Math.max(s.life, 0);
            ctx.fillStyle = s.color;
            ctx.fillRect(s.x - s.size / 2, s.y - s.size / 2, s.size, s.size);
            return true;
        });
        ctx.globalAlpha = 1;

        if (now < endAt || rockets.length || sparks.length) raf = requestAnimationFrame(frame);
        else stop();
    }

    function start(ms) {
        stop();
        canvas.classList.add('on');
        size();
        endAt = performance.now() + ms;
        lastLaunch = 0;
        raf = requestAnimationFrame(frame);
    }

    function stop() {
        cancelAnimationFrame(raf);
        raf = 0; rockets = []; sparks = []; endAt = 0;
        canvas.classList.remove('on');
    }

    return { start, stop };
}
