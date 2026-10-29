/**
 * Panel produktowy: animacja przebiegu pracy AI.
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

const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initPanel() {
    const panel = document.getElementById('panel');
    if (!panel) return;

    const $ = id => document.getElementById(id);
    const stages = ['st-prompt', 'st-work', 'st-result'];
    const explanations = {
        'st-prompt': '1. Ty opisujesz pomysł — bez briefu, bez specyfikacji.',
        'st-work': '2. AI realizuje zadania — widzisz postęp na żywo.',
        'st-result': '3. Gotowe — publikujesz lub prosisz o poprawki.'
    };
    let timers = [];

    const after = (ms, fn) => timers.push(setTimeout(fn, reduce ? 0 : ms));

    function show(id) {
        stages.forEach(s => $(s).hidden = s !== id);
        const explainEl = $('p-explain');
        if (explainEl && explanations[id]) {
            explainEl.textContent = explanations[id];
        }
    }

    function reset() {
        timers.forEach(clearTimeout);
        timers = [];
        $('p-typed').textContent = '';
        $('p-enter').classList.remove('hit');
        $('p-tasks').innerHTML = '';
        $('p-log').innerHTML = '';
        $('p-spent').textContent = '0,00 zł';
        $('p-dec').textContent = '0';
        $('p-week').textContent = '0 zł z 300 zł';
        $('p-meter').style.width = '0';
        $('p-pub').innerHTML = '<button class="chip" type="button" data-ok>Opublikuj stronę</button><button class="chip ghost" type="button" data-no>Popraw</button>';
        const site = document.querySelector('.site');
        if (site) site.classList.remove('in');
        show('st-prompt');
    }

    function play() {
        reset();

        // Stage 1: typing
        const speed = 70;
        let i = 0;

        if (reduce) {
            $('p-typed').textContent = PROMPT;
            i = PROMPT.length;
        } else {
            const t = setInterval(() => {
                $('p-typed').textContent = PROMPT.slice(0, ++i);
                if (i >= PROMPT.length) clearInterval(t);
            }, speed);
            timers.push(t);
        }

        const typed = reduce ? 0 : PROMPT.length * speed;

        after(typed + 600, () => $('p-enter').classList.add('hit'));
        after(typed + 1100, () => {
            show('st-work');
            runWork();
        });
    }

    function runWork() {
        const step = 2400;

        TASKS.forEach(([area, title, desc, ask], i) => {
            after(300 + i * step, () => {
                const el = document.createElement('div');
                el.className = 'task run';
                el.innerHTML = `<div class="h"><span>${title}</span><span class="st">w toku</span></div><p>${desc}</p><div class="area">${area}</div>`;
                const tasks = $('p-tasks');
                tasks.appendChild(el);
                requestAnimationFrame(() => {
                    el.classList.add('in');
                    tasks.scrollTo({ top: tasks.scrollHeight, behavior: 'smooth' });
                });

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
                requestAnimationFrame(() => li.classList.add('in'));

                if (i === 3) {
                    $('p-spent').textContent = '14,00 zł';
                    $('p-week').textContent = '14 zł z 300 zł';
                    $('p-meter').style.width = '5%';
                }
            });
        });

        after(600 + TASKS.length * step + 900, () => {
            show('st-result');
            after(80, () => {
                const site = document.querySelector('.site');
                if (site) site.classList.add('in');
            });
            after(14000, play);
        });
    }

    // Publish/Correct buttons
    $('p-pub').addEventListener('click', e => {
        const b = e.target.closest('.chip');
        if (!b) return;
        $('p-pub').innerHTML = `<span class="chip done">${b.hasAttribute('data-ok') ? 'Opublikowano · wpis w dzienniku' : 'Uwagi zapisane · poprawiam'}</span>`;
        $('p-dec').textContent = '0';
        timers.forEach(clearTimeout);
        timers = [];
        after(10000, play);
    });

    // Replay button
    const replayBtn = document.getElementById('p-replay');
    if (replayBtn) {
        replayBtn.addEventListener('click', play);
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
