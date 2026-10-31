/**
 * Scena intro (1920×1080) do nagrania filmu z hero.
 * Start: window.__studioStart() albo ?auto w adresie. Skrypt: scripts/record-intro.mjs.
 *
 * Przebieg:
 *   kafelek z logo → klik → okno aplikacji → klik „Utwórz firmę" → kamera na pole opisu → pisanie → Enter
 *   → dla każdego zadania: karta wjeżdża, kamera centruje na niej (zbliżenie zostaje), kroki odhaczają się, pasek rośnie, karta się zwija
 *   → kamera na dziennik: wpisy, licznik wydatków, raport → odjazd
 *   → kursor klika „Opublikuj stronę" → konsola wdrożenia (linie + pasek postępu) → telefon z mobilną wersją strony, treść wolno przewija.
 *
 * Kamera: focusFit(el, scale) ustawia transform sceny tak, by element był na środku kadru (skala maleje, gdy element nie mieści się w kadrze).
 * Wersja na telefon (?portrait, kadr 1080×1920): ten sam przebieg, okno ułożone w pionie, kamera jeździ góra–dół.
 */

import { sceneRect, focusFit, camReset, portrait } from './studio-cam.js';

// Ikony marek (inline SVG) przygotowane po stronie PHP – patrz <script type="application/json" id="brand-icons"> w widoku.
const ICONS = JSON.parse(document.getElementById('brand-icons')?.textContent || '{}');
const bi = (id) => ICONS[id] || '';

const PROMPT = 'Chcę zrobić portal dla mojej dzielnicy: seniorzy i rodzice z małymi dziećmi znajdą tu pomoc — sprzątanie, zakupy, spacer z psem, drobne naprawy. Pomocnicy mają profile z opiniami, płatność idzie przez portal.';

// [obszar, tytuł, kroki (tekst, dopisek), status końcowy]
const TASKS = [
    ['Research', 'Analiza podobnych portali', [
        ['Przeglądam 6 serwisów sąsiedzkich w Polsce', '6 źródeł'],
        ['Luka: brak oferty dla seniorów i rodziców w jednej dzielnicy', ''],
        ['Notatka z wnioskami w dzienniku', ''],
    ], 'done'],
    ['Strona', 'Nazwa, domena i strona główna', [
        ['Nazwa: „Pomocni z Dzielnicy"', ''],
        ['Domena pomocnizdzielnicy.pl wolna · rezerwuję', '14 zł'],
        ['Strona główna: nagłówek, wyszukiwarka, 4 kategorie', 'wdrożone'],
    ], 'done'],
    ['Profile', 'Profile pomocników z opiniami', [
        ['Zdjęcie, zakres usług, stawka godzinowa', ''],
        ['Oceny po każdej wykonanej usłudze', ''],
        ['Weryfikacja dowodem przed pierwszym zleceniem', 'wdrożone'],
    ], 'done'],
    ['Płatności', 'Płatność przez portal', [
        [`${bi('blik')}BLIK i ${bi('przelewy24')}Przelewy24 podpięte`, 'tryb testowy'],
        ['Wypłata pomocnikowi po potwierdzeniu wykonania', ''],
        ['Regulamin i polityka prywatności · szkic', 'do przejrzenia'],
    ], 'done'],
    ['Reklamy', `Kampania ${bi('google-ads')}Google Ads`, [
        ['Słowa kluczowe: „pomoc seniorom” + dzielnica', '12 fraz'],
        ['Budżet 40 zł dziennie, start po Twojej zgodzie', ''],
        ['Czeka na Twoją decyzję', ''],
    ], 'ask'],
];

const LOG = [
    ['0:00', 'analizuję opis: 4 usługi, 2 grupy odbiorców, profile, płatności'],
    ['0:04', '6 podobnych portali przejrzanych · notatka w dzienniku'],
    ['0:11', 'domena pomocnizdzielnicy.pl · zarezerwowana · 14 zł'],
    ['0:19', 'strona główna: wersja 1 wdrożona'],
    ['0:27', 'profile pomocników · formularz i oceny · wdrożone'],
    ['0:34', `${bi('przelewy24')}Przelewy24 i ${bi('blik')}BLIK · tryb testowy`],
    ['0:41', 'regulamin i polityka prywatności · szkic do akceptacji'],
    ['0:47', `kampania ${bi('google-ads')}Google Ads · czeka na decyzję`],
    ['0:52', 'raport gotowy · 5 zadań, 1 do decyzji'],
];

const EXPLAIN = {
    prompt: '1. Ty opisujesz pomysł — bez briefu, bez specyfikacji.',
    work: '2. Agenci realizują zadania — widzisz każdy krok.',
    log: '3. Dziennik: każda zmiana i każdy wydatek.',
    deploy: '4. Publikujesz jednym kliknięciem.',
};

// Konsola wdrożenia: [klasa, tekst, procent po tej linii]
const DEPLOY = [
    ['p', '$ udaina deploy pomocnizdzielnicy.pl', 4],
    ['', '→ budowanie strony … 6 podstron, wersja mobilna i desktop', 18],
    ['', '→ obrazy i czcionki … zoptymalizowane (−68%)', 31],
    ['', `→ płatności … ${bi('blik')}BLIK · ${bi('przelewy24')}Przelewy24 · tryb produkcyjny`, 46],
    ['', '→ formularz zgłoszeń i profile pomocników … podpięte', 58],
    ['', '→ certyfikat SSL … wystawiony', 70],
    ['', '→ DNS pomocnizdzielnicy.pl … wskazuje na serwer', 82],
    ['d', '  test mobilny 98/100 · czas ładowania 1,1 s', 93],
    ['g', '✓ opublikowano · https://pomocnizdzielnicy.pl', 100],
];
const DEPLOY_STEP = 260;

const TYPE_MS = 20;
const STEP_MS = 360;     // odstęp między krokami w zadaniu

const $ = (id) => document.getElementById(id);
let timers = [];
const at = (ms, fn) => timers.push(setTimeout(fn, ms));

/* ---------- Kursor ---------- */
function moveCursor(x, y) {
    const c = $('cursor');
    c.style.left = `${x}px`;
    c.style.top = `${y}px`;
}

function moveCursorTo(el) {
    const r = sceneRect(el);
    moveCursor(r.cx - 4, r.cy - 3); // czubek strzałki na środku elementu
}

function click(target) {
    const c = $('cursor');
    c.classList.add('click');
    target?.classList.add('press');
    setTimeout(() => {
        c.classList.remove('click');
        target?.classList.remove('press');
    }, 140);
}

/* ---------- Panel ---------- */
function show(stage) {
    ['st-prompt', 'st-work', 'st-deploy'].forEach((s) => { $(s).hidden = s !== stage; });
}

function explain(key) { $('p-explain').textContent = EXPLAIN[key]; }

function reset() {
    timers.forEach(clearTimeout);
    timers = [];
    // setAttribute, bo kursor to SVG (className nie jest tam zwykłym stringiem).
    $('m-scroll').style.transform = 'none';
    ['tile', 'win', 'start', 'panel', 'cursor', 'phone', 'toast', 'p-report', 'term'].forEach((id) => {
        const el = $(id);
        el.setAttribute('class', el.getAttribute('class').split(' ')[0]);
    });
    camReset(0);
    $('panel').hidden = true;
    $('p-report').hidden = true;
    $('p-typed').textContent = '';
    $('p-enter').classList.remove('hit');
    $('p-tasks').innerHTML = '';
    $('p-log').innerHTML = '';
    $('p-spent').textContent = '0,00 zł';
    $('p-dec').textContent = '0';
    $('p-week').textContent = '0 zł z 300 zł';
    $('p-meter').style.width = '0';
    $('p-deploy').className = 'deploy';
    $('p-deploy').disabled = true;
    $('t-out').innerHTML = '';
    $('t-pct').textContent = '0%';
    $('t-prog').style.width = '0';
    $('t-prog').classList.remove('done');
    moveCursor(1480, 980);
    show('st-prompt');
    explain('prompt');
}

function typePrompt() {
    let i = 0;
    const t = setInterval(() => {
        $('p-typed').textContent = PROMPT.slice(0, ++i);
        if (i >= PROMPT.length) clearInterval(t);
    }, TYPE_MS);
    timers.push(t);
}

function logLine(i) {
    const [time, text] = LOG[i];
    const li = document.createElement('li');
    li.innerHTML = `<time>${time}</time><span>${text}</span>`;
    $('p-log').appendChild(li);
    requestAnimationFrame(() => li.classList.add('in'));
}

function spend() {
    $('p-spent').textContent = '14,00 zł';
    $('p-week').textContent = '14 zł z 300 zł';
    $('p-meter').style.width = '5%';
}

/* Jedno zadanie: karta → zbliżenie → kroki → odjazd → zwinięcie. Zwraca długość cyklu. */
function runTask(startAt, index) {
    const [area, title, steps, final] = TASKS[index];
    let el;

    at(startAt, () => {
        el = document.createElement('div');
        el.className = 'task run';
        el.innerHTML = `<div class="h"><span class="title">${title}<span class="area">${area}</span></span><span class="st">w toku</span></div>`
            + `<ul class="steps">${steps.map(([t, e]) => `<li><span>${t}</span>${e ? `<em>${e}</em>` : ''}</li>`).join('')}</ul>`
            + `<div class="prog"><i></i></div>`;
        $('p-tasks').appendChild(el);
        requestAnimationFrame(() => el.classList.add('in'));
    });

    at(startAt + 140, () => focusFit(el, 2.05, { ms: index === 0 ? 520 : 480, pad: 60 }));

    steps.forEach((_, i) => {
        at(startAt + 420 + i * STEP_MS, () => {
            const items = el.querySelectorAll('.steps li');
            items[i].classList.add('in');
            if (i > 0) items[i - 1].classList.add('ok');
            el.querySelector('.prog i').style.width = `${Math.round(((i + 0.5) / steps.length) * 100)}%`;
        });
    });

    const doneAt = startAt + 420 + steps.length * STEP_MS;
    at(doneAt, () => {
        const items = el.querySelectorAll('.steps li');
        const last = items[items.length - 1];
        el.querySelector('.prog i').style.width = '100%';
        el.classList.remove('run');
        if (final === 'ask') {
            last.classList.add('wait');
            el.classList.add('ask');
            el.querySelector('.st').textContent = 'do decyzji';
            $('p-dec').textContent = '1';
        } else {
            last.classList.add('ok');
            el.querySelector('.st').textContent = 'gotowe';
        }
        if (index === 1) spend();
        logLine(index + 1);
    });

    // Bez odjazdu kamery: karta zwija się w kadrze, a kamera przesuwa się na następną, gdy ta wjedzie.
    at(doneAt + 300, () => el.classList.add('done'));

    return doneAt + 300 + 350 - startAt;
}

export function start() {
    reset();

    // Pulpit: kafelek, klik, okno
    at(200, () => $('tile').classList.add('in'));
    at(1100, () => { $('cursor').classList.add('show'); moveCursorTo($('tile').querySelector('.tile-ico')); });
    at(2000, () => click($('tile')));
    at(2300, () => $('tile').classList.add('out'));
    at(2450, () => $('win').classList.add('in'));

    // „Utwórz firmę"
    at(3300, () => moveCursorTo($('start-btn')));
    at(4100, () => click($('start-btn')));
    at(4300, () => {
        $('start').classList.add('out');
        $('panel').hidden = false;
        requestAnimationFrame(() => $('panel').classList.add('in'));
    });
    at(4400, () => focusFit($('ask-box'), 1.5, { ms: 1100, dy: -10 }));
    at(4500, () => $('cursor').classList.remove('show'));

    // Opis pomysłu
    const typingStart = 4900;
    at(typingStart, typePrompt);
    const typingEnd = typingStart + PROMPT.length * TYPE_MS;
    at(typingEnd + 350, () => $('p-enter').classList.add('hit'));

    // Zadania
    const workStart = typingEnd + 800;
    at(workStart, () => { show('st-work'); explain('work'); camReset(600); logLine(0); });
    let t = workStart + 700;
    TASKS.forEach((_, i) => { t += runTask(t, i); });
    const tasksEnd = t;

    // Dziennik
    const logAt = tasksEnd + 100;
    at(logAt, () => { explain('log'); focusFit($('col-log'), 1.6, { ms: 700, dx: portrait() ? 0 : -150, dy: -40 }); });
    [6, 7, 8].forEach((i, k) => at(logAt + 500 + k * 320, () => logLine(i)));
    at(logAt + 1500, () => { $('p-report').hidden = false; requestAnimationFrame(() => $('p-report').classList.add('in')); });
    at(logAt + 1500, () => { $('p-deploy').disabled = false; $('p-deploy').classList.add('ready'); });
    const logEnd = logAt + 2500;
    at(logEnd, () => camReset(650));

    // Publikacja: klik → konsola wdrożenia z paskiem postępu
    const deployAt = logEnd + 500;
    at(deployAt, () => { explain('deploy'); $('cursor').classList.add('show'); moveCursorTo($('p-deploy')); });
    at(deployAt + 850, () => click($('p-deploy')));
    at(deployAt + 1100, () => {
        $('cursor').classList.remove('show');
        show('st-deploy');
        requestAnimationFrame(() => $('term').classList.add('in'));
    });
    at(deployAt + 1250, () => focusFit($('term'), 1.45, { ms: 750, pad: 60 }));
    const termStart = deployAt + 1700;
    DEPLOY.forEach(([cls, text, pct], i) => {
        at(termStart + i * DEPLOY_STEP, () => {
            $('t-out').querySelector('.cur')?.remove();
            const ln = document.createElement('span');
            ln.className = `ln ${cls}`;
            ln.innerHTML = text + (i < DEPLOY.length - 1 ? '' : '');
            $('t-out').appendChild(ln);
            if (i < DEPLOY.length - 1) {
                const cur = document.createElement('span');
                cur.className = 'cur';
                $('t-out').appendChild(cur);
            }
            requestAnimationFrame(() => ln.classList.add('in'));
            $('t-prog').style.width = `${pct}%`;
            $('t-pct').textContent = `${pct}%`;
            if (pct === 100) $('t-prog').classList.add('done');
        });
    });
    const termEnd = termStart + DEPLOY.length * DEPLOY_STEP;

    // Telefon z mobilną wersją strony
    const phoneAt = termEnd + 700;
    at(phoneAt, () => { $('phone').classList.add('in'); $('win').classList.add('dim'); });
    at(phoneAt + 80, () => focusFit($('phone'), portrait() ? 2 : 1.08, { ms: 950, dy: 12, pad: 100 }));
    at(phoneAt + 900, () => $('toast').classList.add('in'));
    at(phoneAt + 1800, () => {
        // Przewijamy treść telefonu do końca (tyle, ile jej jest), zostawiając miejsce na przycisk u dołu.
        const sc = $('m-scroll');
        const visible = $('m').clientHeight - sc.offsetTop;
        sc.style.transform = `translateY(-${Math.max(0, sc.scrollHeight - visible)}px)`;
    });
    const total = phoneAt + 1800 + 5600;

    window.__studioTimeline = { typingStart, typingEnd, workStart, tasksEnd, logAt, deployAt, termStart, phoneAt, total };
    return window.__studioTimeline;
}

window.__studioStart = start;

if (new URLSearchParams(location.search).has('auto')) {
    window.addEventListener('load', () => document.fonts.ready.then(() => setTimeout(start, 300)));
}
