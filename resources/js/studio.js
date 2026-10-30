/**
 * Scena intro (1920×1080) do nagrania filmu z hero.
 * Start: window.__studioStart() albo ?auto w adresie. Skrypt: scripts/record-intro.mjs.
 *
 * Przebieg:
 *   kafelek z logo → klik → okno aplikacji → klik „Utwórz firmę" → kamera na pole opisu → pisanie → Enter
 *   → dla każdego zadania: karta wjeżdża, kamera centruje na niej (zbliżenie zostaje), kroki odhaczają się, pasek rośnie, karta się zwija
 *   → kamera na dziennik: wpisy, licznik wydatków, raport → odjazd
 *   → kursor klika „Opublikuj stronę" → okno przeglądarki ze stroną → kamera na przeglądarkę.
 *
 * Kamera: focus(el, scale) ustawia transform sceny tak, by element był na środku kadru.
 */

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
        ['BLIK i Przelewy24 podpięte', 'tryb testowy'],
        ['Wypłata pomocnikowi po potwierdzeniu wykonania', ''],
        ['Regulamin i polityka prywatności · szkic', 'do przejrzenia'],
    ], 'done'],
    ['Reklamy', 'Kampania Google Ads', [
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
    ['0:34', 'Przelewy24 i BLIK · tryb testowy'],
    ['0:41', 'regulamin i polityka prywatności · szkic do akceptacji'],
    ['0:47', 'kampania Google Ads · czeka na decyzję'],
    ['0:52', 'raport gotowy · 5 zadań, 1 do decyzji'],
];

const EXPLAIN = {
    prompt: '1. Ty opisujesz pomysł — bez briefu, bez specyfikacji.',
    work: '2. Agenci realizują zadania — widzisz każdy krok.',
    log: '3. Dziennik: każda zmiana i każdy wydatek.',
    deploy: '4. Publikujesz jednym kliknięciem.',
};

const TYPE_MS = 20;
const STEP_MS = 360;     // odstęp między krokami w zadaniu

const $ = (id) => document.getElementById(id);
let timers = [];
const at = (ms, fn) => timers.push(setTimeout(fn, ms));

/* ---------- Kamera ---------- */
const scene = () => $('scene');

function sceneMatrix() {
    const t = getComputedStyle(scene()).transform;
    return t === 'none' ? new DOMMatrix() : new DOMMatrix(t);
}

// Prostokąt elementu we współrzędnych sceny (niezależnie od aktualnego transformu kamery).
function sceneRect(el) {
    const inv = sceneMatrix().inverse();
    const r = el.getBoundingClientRect();
    const a = inv.transformPoint(new DOMPoint(r.left, r.top));
    const b = inv.transformPoint(new DOMPoint(r.right, r.bottom));
    return { x: a.x, y: a.y, w: b.x - a.x, h: b.y - a.y, cx: (a.x + b.x) / 2, cy: (a.y + b.y) / 2 };
}

function focus(el, s, { ms = 650, dx = 0, dy = 0 } = {}) {
    const r = sceneRect(el);
    const sc = scene();
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = `translate(${960 - (r.cx + dx) * s}px, ${540 - (r.cy + dy) * s}px) scale(${s})`;
}

function camReset(ms = 650) {
    const sc = scene();
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = 'none';
}

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
    ['st-prompt', 'st-work'].forEach((s) => { $(s).hidden = s !== stage; });
}

function explain(key) { $('p-explain').textContent = EXPLAIN[key]; }

function reset() {
    timers.forEach(clearTimeout);
    timers = [];
    // setAttribute, bo kursor to SVG (className nie jest tam zwykłym stringiem).
    ['tile', 'win', 'start', 'panel', 'cursor', 'browser', 'toast', 'p-report'].forEach((id) => {
        const el = $(id);
        el.setAttribute('class', el.getAttribute('class').split(' ')[0]);
    });
    scene().style.transform = 'none';
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

    at(startAt + 140, () => focus(el, 2.05, { ms: index === 0 ? 520 : 480 }));

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
    at(4400, () => focus($('ask-box'), 1.5, { ms: 1100, dy: -10 }));
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
    at(logAt, () => { explain('log'); focus($('col-log'), 1.6, { ms: 700, dx: -150, dy: -40 }); });
    [6, 7, 8].forEach((i, k) => at(logAt + 500 + k * 320, () => logLine(i)));
    at(logAt + 1500, () => { $('p-report').hidden = false; requestAnimationFrame(() => $('p-report').classList.add('in')); });
    at(logAt + 1500, () => { $('p-deploy').disabled = false; $('p-deploy').classList.add('ready'); });
    const logEnd = logAt + 2500;
    at(logEnd, () => camReset(650));

    // Publikacja
    const deployAt = logEnd + 500;
    at(deployAt, () => { explain('deploy'); $('cursor').classList.add('show'); moveCursorTo($('p-deploy')); });
    at(deployAt + 850, () => click($('p-deploy')));
    at(deployAt + 1100, () => { $('browser').classList.add('in'); });
    at(deployAt + 1200, () => { $('cursor').classList.remove('show'); focus($('browser'), 1.28, { ms: 1000 }); });
    at(deployAt + 2000, () => $('toast').classList.add('in'));
    const total = deployAt + 2000 + 4500;

    window.__studioTimeline = { typingStart, typingEnd, workStart, tasksEnd, logAt, deployAt, total };
    return window.__studioTimeline;
}

window.__studioStart = start;

if (new URLSearchParams(location.search).has('auto')) {
    window.addEventListener('load', () => document.fonts.ready.then(() => setTimeout(start, 300)));
}
