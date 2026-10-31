/**
 * Scena WhatsApp (1920×1080) – drugi film w hero (zakładka „WhatsApp”).
 * Start: window.__studioStart() albo ?auto. Nagranie: npm run record:whatsapp.
 *
 * Przebieg: właściciel pisze „jak idzie?” → agent odpisuje raportem → prosi o decyzję
 * (trend w dzielnicy, kampania Google Ads na 2 tygodnie) → „Tak, ale limit 300 zł” → potwierdzenie → 👍.
 * Lista wiadomości jest przypięta do dołu; nowa wiadomość wsuwa się, a reszta płynnie jedzie w górę (FLIP).
 */
import { focus, camReset, portrait } from './studio-cam.js';

const ICONS = JSON.parse(document.getElementById('brand-icons')?.textContent || '{}');
const bi = (id) => ICONS[id] || '';

const TYPE_MS = 52;

const TICKS = '<svg class="ticks" viewBox="0 0 18 12" aria-hidden="true"><path d="m1 6.5 3.2 3.2L10.5 3M7.5 9.7l1 1L16.8 3" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>';

// Podpisy po lewej: [nagłówek, dopisek]
const CAPS = [
    ['Pytasz, kiedy chcesz.', 'Zwykła wiadomość, żadnego panelu.'],
    ['Dostajesz raport, nie dashboard.', 'Liczby z dziś i to, co agent naprawił po drodze.'],
    ['Decydujesz jednym zdaniem.', 'Agent widzi trend, Ty mówisz „tak” albo „zmień”.'],
    ['Reszta dzieje się sama.', 'Kampania, podstrona, raport za trzy dni – bez Twojego udziału.'],
];

const MSG = {
    yesterday: `✅ Strona działa: <b>pomocnizdzielnicy.pl</b><br>Płatności ${bi('blik')}BLIK i ${bi('przelewy24')}Przelewy24 włączone, pierwsze 3 zgłoszenia już są. Dobranoc 🌙`,
    ask: 'Hej, jak idzie praca?',
    hi: 'Cześć! Krótki raport z dziś 👇',
    report: `📊 <b>Pomocni z Dzielnicy · dziś</b><br>✅ 12 zgłoszeń (wczoraj 7)<br>✅ 3 nowych pomocników<br>💳 1&nbsp;840 zł przez portal<br>⭐ 4,9 z 48 opinii<br>🔧 Formularz na iPhonie – naprawiony<br>💸 Wydane dziś: 6,40 zł`,
    one: 'I jedna rzecz do decyzji:',
    trend: `📈 <b>Trend w dzielnicy</b><br>Przy Bokserskiej oddano nowe osiedle. Wyszukiwania „pomoc w przeprowadzce” w okolicy: <b>+140% w tydzień</b>.<br><br>Propozycja: kampania ${bi('google-ads')}<b>Google Ads</b> na 2 tygodnie, 30 zł/dzień (420 zł) + podstrona „Pomoc przy przeprowadzce”. Szacuję 25–40 zgłoszeń.<br><br>Odpalamy? Odpisz <b>TAK</b> albo co zmienić.`,
    yes: 'Tak, ale limit 300 zł łącznie',
    ok: '✅ Przyjęte: 2 tygodnie, limit <b>300 zł</b> – przy tej kwocie kampania zatrzyma się sama. Start dziś o 18:00, podstrona będzie za ~10 min. Raport z pierwszych 3 dni wyślę tu w czwartek.',
};

const $ = (id) => document.getElementById(id);
let timers = [];
const at = (ms, fn) => timers.push(setTimeout(fn, ms));

/* ---------- Lista wiadomości ---------- */
// Dodaje element na dół listy; lista przypięta jest do dołu, więc reszta płynnie odjeżdża w górę.
function push(el) {
    const list = $('wa-list');
    list.style.transition = 'none';
    list.appendChild(el);
    const h = el.offsetHeight + parseFloat(getComputedStyle(el).marginTop || 0);
    list.style.transform = `translateY(${h}px)`;
    list.getBoundingClientRect(); // reflow
    list.style.transition = '';
    list.style.transform = 'translateY(0)';
    requestAnimationFrame(() => el.classList.add('in'));
    return el;
}

function day(text) {
    const d = document.createElement('div');
    d.className = 'day';
    d.textContent = text;
    return push(d);
}

function msg(dir, html, time, { read = true, today = true } = {}) {
    if (today) $('wa-clock').textContent = time; // zegar w pasku statusu idzie z rozmową
    const m = document.createElement('div');
    m.className = `msg ${dir}`;
    m.innerHTML = `<span class="txt">${html}</span><span class="meta">${time}${dir === 'out' ? TICKS : ''}</span>`;
    push(m);
    if (dir === 'out') {
        at(250, () => m.querySelector('.ticks').classList.add('two'));
        if (read) at(900, () => m.querySelector('.ticks').classList.add('read'));
    }
    return m;
}

let typingEl = null;
function typing(on) {
    $('wa-sub').textContent = on ? 'pisze…' : 'online';
    if (on && !typingEl) {
        typingEl = document.createElement('div');
        typingEl.className = 'msg in typing';
        typingEl.innerHTML = '<i></i><i></i><i></i>';
        push(typingEl);
    } else if (!on && typingEl) {
        typingEl.remove();
        typingEl = null;
    }
}

function react(m, emoji) {
    const r = document.createElement('span');
    r.className = 'react';
    r.textContent = emoji;
    m.classList.add('reacted');
    m.appendChild(r);
    requestAnimationFrame(() => r.classList.add('in'));
}

/* ---------- Pisanie w polu ---------- */
function typeOut(text) {
    $('wa-foot').classList.add('focus');
    let i = 0;
    const t = setInterval(() => {
        $('wa-typed').textContent = text.slice(0, ++i);
        if (i === 1) $('wa-foot').classList.add('has-text');
        if (i >= text.length) clearInterval(t);
    }, TYPE_MS);
    timers.push(t);
    return text.length * TYPE_MS;
}

function send(text, time) {
    $('wa-send').classList.add('press');
    setTimeout(() => $('wa-send').classList.remove('press'), 140);
    $('wa-typed').textContent = '';
    $('wa-foot').classList.remove('has-text', 'focus');
    return msg('out', text, time);
}

function cap(i) {
    const c = $('cap');
    c.classList.remove('in');
    setTimeout(() => {
        $('cap-t').textContent = CAPS[i][0];
        $('cap-s').textContent = CAPS[i][1];
        c.classList.add('in');
    }, 260);
}

function reset() {
    timers.forEach(clearTimeout);
    timers = [];
    typingEl = null;
    $('wa-list').innerHTML = '';
    $('wa-list').style.transform = 'translateY(0)';
    $('wa-typed').textContent = '';
    $('wa-foot').className = 'wa-foot';
    $('wa-sub').textContent = 'online';
    $('wa-clock').textContent = '13:37';
    $('phone').classList.remove('in');
    $('cap').classList.remove('in');
    camReset(0);
}

export function start() {
    reset();

    // Wczorajsza wiadomość już w rozmowie (łączy ten film z pierwszym: strona wdrożona).
    day('wczoraj');
    msg('in', MSG.yesterday, '18:42', { today: false });
    day('dzisiaj');
    $('wa-list').style.transition = 'none';
    $('wa-list').querySelectorAll('.day, .msg').forEach((el) => el.classList.add('in'));

    at(150, () => $('phone').classList.add('in'));
    at(600, () => cap(0));

    // 1. „Jak idzie?”
    const t1 = 1300;
    at(t1, () => typeOut(MSG.ask));
    const sent1 = t1 + MSG.ask.length * TYPE_MS + 450;
    at(sent1, () => send(MSG.ask, '13:37'));

    // 2. Raport
    at(sent1 + 700, () => typing(true));
    at(sent1 + 1900, () => { typing(false); msg('in', MSG.hi, '13:37'); });
    at(sent1 + 2500, () => typing(true));
    const reportAt = sent1 + 3700;
    at(reportAt - 200, () => cap(1));
    at(reportAt, () => { typing(false); msg('in', MSG.report, '13:38'); });
    // Zbliżenie na resztę rozmowy: telefon zostaje w miejscu, dolna krawędź równo z kadrem (pasek statusu wychodzi górą).
    // W pionie telefon i tak wypełnia kadr, a nad nim siedzi podpis – kamera stoi.
    if (!portrait()) at(reportAt + 450, () => focus($('phone'), 1.12, { ms: 1000, dx: -241, bottom: 0 }));

    // 3. Decyzja
    const oneAt = reportAt + 5200;
    at(oneAt - 1200, () => typing(true));
    at(oneAt, () => { typing(false); msg('in', MSG.one, '13:38'); });
    at(oneAt + 500, () => typing(true));
    const trendAt = oneAt + 1900;
    at(trendAt - 200, () => cap(2));
    at(trendAt, () => { typing(false); msg('in', MSG.trend, '13:38'); });

    // 4. „Tak, ale…”
    const t2 = trendAt + 6200;
    at(t2, () => typeOut(MSG.yes));
    const sent2 = t2 + MSG.yes.length * TYPE_MS + 450;
    at(sent2, () => send(MSG.yes, '13:39'));

    // 5. Potwierdzenie i 👍
    at(sent2 + 800, () => typing(true));
    const okAt = sent2 + 2300;
    at(okAt - 200, () => cap(3));
    let ok;
    at(okAt, () => { typing(false); ok = msg('in', MSG.ok, '13:39'); });
    if (!portrait()) at(okAt + 500, () => camReset(1000));
    at(okAt + 2600, () => react(ok, '👍'));
    const total = okAt + 2600 + 3400;

    window.__studioTimeline = { sent1, reportAt, trendAt, sent2, okAt, total };
    return window.__studioTimeline;
}

window.__studioStart = start;
if (new URLSearchParams(location.search).has('auto')) {
    start();
}
