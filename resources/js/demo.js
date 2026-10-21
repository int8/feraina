/**
 * Demo „Ferajna w akcji”: pusty panel wypełnia się na oczach użytkownika.
 * Kolumny wjeżdżają po kolei z mikroopisem, potem rozdziały: liczby → zadania → kanały → decyzje.
 * Każdy nowy element dostaje na chwilę oznaczenie „nowe”, żeby diff był widoczny.
 * Serwer renderuje stan końcowy (bez JS i przy prefers-reduced-motion zostaje jak jest).
 */
export function initDemo(root) {
    const dataEl = root.querySelector('script[type="application/json"]');
    if (!dataEl) return;
    const data = JSON.parse(dataEl.textContent);
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

    const $ = (sel) => root.querySelector(sel);
    const $$ = (sel) => [...root.querySelectorAll(sel)];
    const strip = $('[data-demo-strip]');
    const cols = $$('[data-demo-col]');
    const tasks = $('[data-demo-tasks]');
    const docs = $('[data-demo-docs]');
    const today = $('[data-demo-today]');
    const channels = $('[data-demo-channels]');
    const budgetEl = $('[data-demo-budget]');
    const decisionsEl = $('[data-demo-decisions]');
    const stepEl = $('[data-demo-step]');
    const ship = $('[data-demo-ship]');
    const metrics = $$('[data-demo-metric]');
    const ask = $('[data-demo-ask]');
    const companyEl = $('[data-demo-company]');
    const debug = $('[data-demo-debug]');
    const metricLabels = $$('[data-demo-metric-label]');

    const order = shuffle(data.sessions.map((_, i) => i));
    let cursor = 0;
    let gen = 0; // numer sesji, żeby stara pętla nie pisała po nowej

    const fmt = (n) => n.toFixed(2).replace('.', ',');
    const setBudget = (n) => budgetEl && (budgetEl.textContent = `${fmt(n)} / ${data.budget_limit} zł`);
    const setDecisions = (n) => decisionsEl && (decisionsEl.textContent = String(n));
    const setStep = (t) => stepEl && (stepEl.textContent = t);

    const log = (text, kind = 'out') => {
        const li = document.createElement('li');
        li.className = `demo-strip-line is-${kind}`;
        li.textContent = `> ${text}`;
        strip.appendChild(li);
        while (strip.children.length > 4) strip.removeChild(strip.firstChild);
    };

    // Debug-pane: surowy ruch dla aktualnego obszaru. Osobna pętla, żeby „napierdalało” niezależnie od narracji.
    let dbgArea = 'idle';
    const dbg = (text) => {
        if (!debug) return;
        const li = document.createElement('li');
        const t = document.createElement('time');
        const now = new Date();
        t.textContent = now.toLocaleTimeString('pl-PL', { hour12: false }) + '.' + String(now.getMilliseconds()).padStart(3, '0');
        li.appendChild(t);
        li.appendChild(document.createTextNode(text));
        if (text.startsWith('←')) li.className = 'is-resp';
        if (text.startsWith('auth')) li.className = 'is-sec';
        debug.appendChild(li);
        while (debug.children.length > 5) debug.removeChild(debug.firstChild);
    };
    const debugLoop = async (alive) => {
        while (alive()) {
            if (dbgArea === 'idle') {
                dbg(pick(DEBUG.idle)());
                await wait(rnd(1400, 2600));
            } else {
                const pool = Math.random() < 0.72 ? DEBUG[dbgArea] || DEBUG.common : DEBUG.common;
                dbg(pick(pool)());
                await wait(rnd(160, 520));
            }
        }
    };

    const markNew = (el) => {
        el.classList.add('is-new');
        setTimeout(() => el.classList.remove('is-new'), 2600 * TEMPO);
        return el;
    };

    // Reflektor: aktywny kafelek jest na pierwszym planie, reszta panelu lekko gaśnie.
    const focus = (el) => {
        $$('.is-focus, .is-focus-col').forEach((x) => x.classList.remove('is-focus', 'is-focus-col'));
        if (el) {
            el.classList.add('is-focus');
            // Kafelek bywa fokusowany tuż przed wstawieniem do DOM, więc kolumny szukamy chwilę później.
            queueMicrotask(() => el.closest('[data-demo-col]')?.classList.add('is-focus-col'));
            root.classList.add('has-focus');
        } else {
            root.classList.remove('has-focus');
        }
        return el;
    };

    const reset = () => {
        strip.replaceChildren();
        tasks.replaceChildren();
        docs.replaceChildren();
        today.replaceChildren();
        channels.replaceChildren();
        if (debug) debug.replaceChildren();
        dbgArea = 'idle';
        cols.forEach((c) => c.classList.add('is-hidden'));
        ship.hidden = true;
        ask.hidden = true;
        metrics.forEach((m) => (m.textContent = '—'));
        setBudget(0);
        setDecisions(0);
        setStep('start');
        root.classList.remove('is-fading');
        focus(null);
    };

    // --- budowanie elementów ---
    const taskCard = (t) => {
        const el = document.createElement('article');
        el.className = 'demo-task is-running';
        el.innerHTML = `
            <div class="demo-task-head"><h4></h4><span class="demo-status"><i aria-hidden="true"></i><span>w toku</span></span></div>
            <p class="demo-task-outcome"></p>
            <div class="demo-task-foot"><span class="demo-tag"></span><span class="demo-task-time"></span></div>`;
        el.querySelector('h4').textContent = t.start;
        el.querySelector('.demo-tag').textContent = data.areas[t.area] || t.area;
        return el;
    };
    const finishCard = (el, t) => {
        el.classList.remove('is-running');
        el.classList.add(t.decision ? 'is-decision' : 'is-done');
        el.querySelector('h4').textContent = t.done;
        el.querySelector('.demo-status span').textContent = t.decision ? 'do decyzji' : 'gotowe';
        el.querySelector('.demo-task-outcome').textContent = t.outcome;
        markNew(el);
    };
    const docItem = (title, when) => {
        const li = document.createElement('li');
        li.className = 'demo-doc';
        li.innerHTML = '<span class="demo-doc-title"></span><span class="demo-doc-when"></span>';
        li.querySelector('.demo-doc-title').textContent = title;
        li.querySelector('.demo-doc-when').textContent = when;
        return li;
    };
    const channelItem = (title, text, post = false) => {
        const div = document.createElement('div');
        div.className = 'demo-channel';
        div.innerHTML = post ? '<h5></h5><div class="demo-post"><span></span><small></small></div>' : '<h5></h5><p></p>';
        div.querySelector('h5').textContent = title;
        if (post) {
            div.querySelector('span').textContent = text.post;
            div.querySelector('small').textContent = text.meta;
        } else {
            div.querySelector('p').textContent = text;
        }
        return div;
    };
    const note = (text, time, decision = false, onSettle = null) => {
        const li = document.createElement('li');
        li.className = `demo-note${decision ? ' is-decision' : ''}`;
        li.innerHTML = '<p class="demo-note-text"></p>' + (decision ? '<div class="demo-note-actions"><button type="button" class="demo-btn" data-ok>Zatwierdź</button><button type="button" class="demo-btn demo-btn-ghost" data-no>Odrzuć</button></div>' : '') + '<span class="demo-note-time"></span>';
        li.querySelector('.demo-note-text').textContent = text;
        li.querySelector('.demo-note-time').textContent = time;
        if (decision) {
            const settle = (ok) => {
                li.classList.remove('is-decision');
                li.classList.add('is-settled');
                li.querySelector('.demo-note-actions').textContent = ok ? 'Zatwierdzone przez Ciebie' : 'Odrzucone. Nic nie wdrożono.';
                onSettle?.(ok);
            };
            li.querySelector('[data-ok]').addEventListener('click', () => settle(true));
            li.querySelector('[data-no]').addEventListener('click', () => settle(false));
        }
        return li;
    };

    const countUp = async (el, to, unit = '', ms = 900) => {
        const steps = 18;
        for (let i = 1; i <= steps; i++) {
            el.textContent = Math.round((to * i) / steps).toLocaleString('pl-PL') + unit;
            await wait(ms / steps);
        }
    };

    // --- scenariusz sesji ---
    async function session() {
        const my = ++gen;
        const alive = () => my === gen;
        const dash = data.sessions[order[cursor % order.length]];
        cursor += 1;
        reset();
        debugLoop(alive);
        if (companyEl) companyEl.textContent = dash.company;
        dash.metrics.forEach((m, i) => metricLabels[i] && (metricLabels[i].textContent = m.label));
        await wait(600);

        for (const line of dash.intro) {
            log(line, 'start');
            await wait(1100);
        }

        // 1. Kolumny wjeżdżają po kolei
        for (const col of cols) {
            if (!alive()) return;
            col.classList.remove('is-hidden');
            markNew(col);
            await wait(750);
        }
        await wait(500);

        // 2. Firma: liczby i dokumenty
        setStep('liczby');
        dbgArea = 'liczby';
        log('Zbieram liczby z Analytics, skrzynki i Fakturowni.', 'tool');
        ship.hidden = false;
        markNew(ship);
        focus(ship.closest('[data-demo-firma]') || ship);
        for (const m of dash.metrics) {
            const el = metrics.find((x) => x.dataset.demoMetric === m.id);
            if (el) await countUp(el, m.value);
        }
        log('Liczby aktualne. Dokumenty firmy na miejscu.', 'done');
        for (const d of dash.documents) {
            if (!alive()) return;
            docs.appendChild(focus(markNew(docItem(d.title, d.when))));
            await wait(450);
        }
        await wait(700);
        focus(null);
        dbgArea = 'idle';

        // 3. Zadania: trzy z różnych obszarów, jedno po drugim
        setStep('zadania');
        let spent = 0;
        const regular = dash.tasks.filter((t) => !t.decision);
        const done = [];
        for (const t of regular) {
            if (!alive()) return;
            done.push(t);
            const card = taskCard(t);
            tasks.prepend(focus(markNew(card)));
            dbgArea = t.area;
            log(`Zadanie rozpoczęte: ${t.start}`, 'start');
            await wait(700);
            for (const line of t.log) {
                if (!alive()) return;
                log(line.s, line.t);
                await wait(line.t === 'tool' ? 850 : 650);
            }
            finishCard(card, t);
            if (t.spend) {
                spent = Math.min(spent + t.spend, data.budget_limit);
                setBudget(spent);
            }
            log(`Zakończone: ${t.done}`, 'done');
            if (t.area === 'research') docs.prepend(markNew(docItem(t.done, 'teraz')));
            dbgArea = 'idle';
            await wait(900);
        }
        focus(null);

        // 4. Kanały: co z tego wyszło na zewnątrz
        setStep('kanały');
        dbgArea = 'kanaly';
        log('Aktualizuję kanały: reklamy, posty, faktury.', 'tool');
        const ch = dash.channels;
        const items = [
            channelItem(ch.ads.title, ch.ads.line),
            channelItem(ch.social.title, { post: ch.social.post, meta: ch.social.meta }, true),
            channelItem(ch.inbox.title, ch.inbox.line),
            channelItem(ch.invoices.title, ch.invoices.line),
        ];
        for (const it of items) {
            if (!alive()) return;
            channels.appendChild(focus(markNew(it)));
            await wait(650);
        }
        await wait(600);
        focus(null);
        dbgArea = 'idle';

        // 5. Dla Ciebie: jedna decyzja i raport
        setStep('decyzje');
        const decision = dash.tasks.find((t) => t.decision) || regular[regular.length - 1];
        const dcard = taskCard(decision);
        tasks.prepend(focus(markNew(dcard)));
        dbgArea = decision.area;
        log(`Zadanie rozpoczęte: ${decision.start}`, 'start');
        await wait(700);
        for (const line of decision.log) {
            if (!alive()) return;
            log(line.s, line.t);
            await wait(line.t === 'tool' ? 850 : 650);
        }
        finishCard(dcard, decision);
        dbgArea = 'idle';
        if (decision.spend) setBudget(Math.min(spent + decision.spend, data.budget_limit));
        setDecisions(1);
        log(`Do decyzji: ${decision.done}`, 'ask');
        if (decision.area === 'research') docs.prepend(markNew(docItem(decision.done, 'teraz')));
        today.prepend(focus(markNew(note(`${decision.done}. ${decision.outcome}`, 'czeka', true, (ok) => {
            setDecisions(0);
            log(ok ? `Zatwierdzono: ${decision.done}` : `Odrzucono: ${decision.done}`, ok ? 'done' : 'out');
        }))));
        await wait(1800);
        if (!alive()) return;
        const count = done.length + 1;
        today.appendChild(markNew(note(`Raport gotowy. ${count} zadania, z czego 1 czeka na Twoją decyzję. Reszta wdrożona i zapisana w logu.`, 'raport')));
        ask.hidden = false;
        focus(null);
        log('Raport wysłany. Czekam na Twoje decyzje.', 'done');
        setStep('raport');

        // 6. Pauza i nowa sesja
        await wait(9000);
        if (!alive()) return;
        root.classList.add('is-fading');
        await wait(700);
        if (alive()) session();
    }

    // Start dopiero, gdy panel jest widoczny.
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            if (entries.some((e) => e.isIntersecting)) {
                io.disconnect();
                session();
            }
        }, { threshold: 0.2 });
        io.observe(root);
    } else {
        session();
    }
}

// --- Debug-pane: pule linii per obszar. Prawdziwe adresy API, zero sekretów (zawsze „••••”). ---
const rnd = (a, b) => a + Math.floor(Math.random() * (b - a + 1));
const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];
const hex = (n) => [...Array(n)].map(() => '0123456789abcdef'[rnd(0, 15)]).join('');
const ms = () => `${rnd(60, 480)}ms`;
const zl = (a, b) => (rnd(a * 100, b * 100) / 100).toFixed(2).replace('.', ',');
const MASK = '••••••••';
const DEBUG = {
    common: [
        () => `llm · ${rnd(600, 2400).toLocaleString('pl-PL')} tok → ${rnd(80, 420)} tok · ${(rnd(6, 24) / 10).toFixed(1).replace('.', ',')}s`,
        () => `auth · Bearer ${MASK} (ukryte)`,
        () => `budżet · +${zl(0.02, 0.6)} zł · limit 100 zł`,
        () => `cache · hit ${rnd(40, 98)}% · ttl 300s`,
        () => `log · sesja #${hex(4)} · zapis ok`,
        () => `retry · 1/3 · backoff ${rnd(200, 900)}ms`,
    ],
    idle: [
        () => `heartbeat · kolejka 0 · ${ms()}`,
        () => `watch · skrzynka · nowych 0`,
        () => `watch · allegro · zamówień 0`,
        () => `cron · następny przebieg za ${rnd(2, 14)} min`,
        () => `mem · ${rnd(180, 320)} MB · ok`,
    ],
    liczby: [
        () => `GET https://analyticsdata.googleapis.com/v1beta/properties/${MASK}:runReport 200 ${ms()}`,
        () => `← {"rows":[…${rnd(5, 30)}],"metricHeaders":["activeUsers","sessions"]}`,
        () => `GET https://app.fakturownia.pl/invoices.json?period=this_month 200 ${ms()}`,
        () => `GET https://gmail.googleapis.com/gmail/v1/users/me/messages?q=is:unread 200 ${ms()}`,
        () => `agregacja · przychód mies. · ${rnd(4, 40)} faktur · ok`,
    ],
    research: [
        () => `GET https://maps.googleapis.com/maps/api/place/textsearch/json?query=… 200 ${ms()}`,
        () => `GET https://wl-api.mf.gov.pl/api/search/nip/${rnd(1000000000, 9999999999)} 200 ${ms()}`,
        () => `GET https://api-krs.ms.gov.pl/api/krs/OdpisAktualny/… 200 ${ms()}`,
        () => `fetch · ${rnd(6, 18)} stron · robots ok · ${(rnd(8, 30) / 10).toFixed(1).replace('.', ',')}s`,
        () => `← {"results":[…${rnd(8, 40)}],"status":"OK"}`,
        () => `parse · ${rnd(8, 40)} rekordów · dedup → ${rnd(5, 20)}`,
        () => `mediana · ${rnd(30, 90) * 100} zł · n=${rnd(8, 20)}`,
    ],
    reklama: [
        () => `GET https://googleads.googleapis.com/v18/customers/${MASK}/googleAds:search 200 ${ms()}`,
        () => `POST https://googleads.googleapis.com/v18/customers/${MASK}/campaignBudgets:mutate 200 ${ms()}`,
        () => `GET https://graph.facebook.com/v21.0/act_${MASK}/insights?fields=spend,cpc,ctr 200 ${ms()}`,
        () => `← {"spend":"${zl(12, 60)}","cpc":"${zl(0.4, 2.2)}","ctr":"${zl(1, 4)}"}`,
        () => `rate-limit · ${rnd(60, 180)}/200 · ok`,
        () => `walidacja · budżet dzienny ≤ limit sesji · ok`,
    ],
    leady: [
        () => `GET https://gmail.googleapis.com/gmail/v1/users/me/messages?q=is:unread 200 ${ms()}`,
        () => `GET https://graph.facebook.com/v21.0/me/conversations?platform=messenger 200 ${ms()}`,
        () => `POST https://www.googleapis.com/calendar/v3/freeBusy 200 ${ms()}`,
        () => `← {"messages":[…${rnd(1, 5)}],"resultSizeEstimate":${rnd(1, 5)}}`,
        () => `POST https://graph.facebook.com/v21.0/t_${MASK}/messages 200 · wysłane`,
        () => `crm · upsert lead #${rnd(1000, 9999)} · źródło=messenger`,
    ],
    www: [
        () => `GET https://[domena]/ 200 ${ms()} · html ${rnd(40, 140)} kB`,
        () => `lighthouse · perf ${rnd(88, 99)} · a11y ${rnd(92, 100)} · seo ${rnd(90, 100)}`,
        () => `git · commit ${hex(7)} "cennik: aktualizacja" · 2 pliki`,
        () => `deploy · build ${rnd(8, 40)}s · cache purge · live`,
        () => `GET https://www.googleapis.com/webmasters/v3/sites/…/searchAnalytics 200 ${ms()}`,
        () => `← {"rows":[{"keys":["…"],"clicks":${rnd(3, 60)},"position":${(rnd(20, 120) / 10).toFixed(1)}}]}`,
    ],
    tresci: [
        () => `llm · ${rnd(2, 4)} warianty · ton: rzeczowy · ${(rnd(8, 30) / 10).toFixed(1).replace('.', ',')}s`,
        () => `obraz · resize 1080×1350 · ${rnd(120, 420)} kB`,
        () => `POST https://graph.facebook.com/v21.0/${MASK}/photos 200 ${ms()}`,
        () => `POST https://api.linkedin.com/v2/ugcPosts 201 ${ms()}`,
        () => `← {"id":"${rnd(10 ** 14, 10 ** 15 - 1)}_${rnd(10 ** 14, 10 ** 15 - 1)}"}`,
        () => `harmonogram · publikacja ${rnd(8, 20)}:00`,
    ],
    faktury: [
        () => `POST https://app.fakturownia.pl/invoices.json 201 ${ms()}`,
        () => `← {"id":${rnd(40000, 99999)},"number":"FV/2026/10/${String(rnd(1, 999)).padStart(4, '0')}","total":"${zl(200, 9000)}"}`,
        () => `POST https://ksef.mf.gov.pl/api/online/Invoice/Send 202 ${ms()}`,
        () => `← ref 20261021-EE-${hex(8).toUpperCase()} · UPO w kolejce`,
        () => `GET https://secure.przelewy24.pl/api/v1/transaction/by/sessionId/${hex(6)} 200 ${ms()}`,
        () => `dopasowanie · przelew ${zl(200, 9000)} zł ↔ FV · ok`,
    ],
};
DEBUG.kanaly = [...DEBUG.reklama, ...DEBUG.tresci, ...DEBUG.faktury];

// Tempo całej narracji: 0.5 = dwa razy szybciej niż bazowe czasy w kodzie.
const TEMPO = 0.5;
const wait = (ms) => new Promise((r) => setTimeout(r, ms * TEMPO));
const shuffle = (a) => a.map((v) => [Math.random(), v]).sort((x, y) => x[0] - y[0]).map((x) => x[1]);
