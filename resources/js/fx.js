/**
 * Efekty tekstu.
 * - scramble(): litery nagłówka hero przez chwilę pokazują losowe znaki (na zmianę font pikselowy i zwykły), potem właściwą literę;
 *   potem w tle co chwilę jedna litera mignie w losowym kolorze (idleFlicker).
 * - reveal(): nagłówki sekcji dzielimy na litery; po wejściu w viewport każda wjeżdża od dołu, z pomarańczowego do koloru docelowego.
 * Oba efekty wyłączone przy prefers-reduced-motion.
 */

const SCRAMBLE_CHARS = '0123456789!<>-_/[]{}=+*^?#%&$@|~;:';
const IDLE_CHARS = SCRAMBLE_CHARS + 'AEKMNRSTWXZ';
const IDLE_COLORS = ['#ff8f52', '#ffd166', '#7dd3fc', '#a3e635', '#f0abfc'];
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const rand = (n) => (Math.random() * n) | 0;

const settle = (el) => {
    el.style.removeProperty('visibility');
    el.style.removeProperty('--sg');
    el.style.removeProperty('--sg-font');
    el.style.removeProperty('--sg-color');
};

/* Po pierwszym scramble: co chwilę jedna litera mignie losowymi znakami w losowym kolorze i wraca. */
function idleFlicker(chars, { minGap = 1400, maxGap = 3200, ticks = 5, tickMs = 55 } = {}) {
    let timer;
    const flick = () => {
        const el = chars[rand(chars.length)];
        const color = IDLE_COLORS[rand(IDLE_COLORS.length)];
        let i = 0;
        el.style.visibility = 'hidden';
        el.style.setProperty('--sg-color', color);
        const tick = () => {
            if (i++ >= ticks) { settle(el); schedule(); return; }
            el.style.setProperty('--sg', JSON.stringify(IDLE_CHARS[rand(IDLE_CHARS.length)]));
            el.style.setProperty('--sg-font', i % 2 ? 'var(--font-pixel)' : 'inherit');
            timer = setTimeout(tick, tickMs);
        };
        tick();
    };
    const schedule = () => { timer = setTimeout(flick, minGap + rand(maxGap - minGap)); };
    document.addEventListener('visibilitychange', () => {
        clearTimeout(timer);
        if (!document.hidden) schedule();
    });
    schedule();
}

export function scramble(root, { startDelay = 0, charStagger = 20, scrambleMs = 340, tickMs = 40, idle = true } = {}) {
    if (!root || reduce) return;
    const chars = [...root.querySelectorAll('[data-scramble]')];
    if (!chars.length) return;

    chars.forEach((el) => { el.style.visibility = 'hidden'; });
    const start = performance.now();
    const state = chars.map(() => NaN);

    const frame = (now) => {
        const t = now - start - startDelay;
        let pending = false;
        chars.forEach((el, i) => {
            if (state[i] === -1) return;
            const at = i * charStagger;
            if (t < at) { pending = true; return; }
            if (t >= at + scrambleMs) { state[i] = -1; settle(el); return; }
            pending = true;
            const tick = Math.floor((t - at) / tickMs);
            if (state[i] === tick) return;
            state[i] = tick;
            const final = t - at > 0.72 * scrambleMs;
            const glyph = final ? el.dataset.scramble : SCRAMBLE_CHARS[(Math.random() * SCRAMBLE_CHARS.length) | 0];
            el.style.setProperty('--sg', JSON.stringify(glyph));
            el.style.setProperty('--sg-font', tick % 2 === 0 ? 'var(--font-pixel)' : 'inherit');
        });
        if (pending) requestAnimationFrame(frame);
        else if (idle) idleFlicker(chars);
    };
    requestAnimationFrame(frame);
}

export function reveal(el, { duration = 500, stagger = 15, y = 50, from = null, threshold = 0.3, rootMargin = '0px 0px -100px 0px' } = {}) {
    if (!el || el.dataset.revealReady) return;
    const text = el.textContent.replace(/\s+/g, ' ').trim();
    if (!el.hasAttribute('aria-label')) el.setAttribute('aria-label', text);

    const frag = document.createDocumentFragment();
    text.split(' ').forEach((word, i) => {
        if (i) frag.appendChild(document.createTextNode(' '));
        const w = document.createElement('span');
        w.className = 'rv-w';
        w.setAttribute('aria-hidden', 'true');
        for (const ch of word) {
            const c = document.createElement('span');
            c.className = 'rv-c';
            c.textContent = ch;
            w.appendChild(c);
        }
        frag.appendChild(w);
    });
    el.replaceChildren(frag);
    el.dataset.revealReady = '1';

    if (reduce || !('IntersectionObserver' in window)) return;
    const chars = [...el.querySelectorAll('.rv-c')];
    chars.forEach((c) => { c.style.opacity = '0'; });

    const run = () => {
        // Kolory czytamy dopiero teraz: zależą od aktywnego motywu (--reveal-from, kolor nagłówka).
        const start = from || getComputedStyle(document.documentElement).getPropertyValue('--reveal-from').trim() || '#ef6f2e';
        const to = el.dataset.revealTo || getComputedStyle(el).color;
        chars.forEach((c, i) => {
            const anim = c.animate(
                [
                    { opacity: 0, transform: `translateY(${y}px)`, color: start },
                    { opacity: 1, transform: 'translateY(0)', color: to },
                ],
                { duration, delay: i * stagger, easing: 'cubic-bezier(0.645, 0.045, 0.355, 1)', fill: 'forwards' },
            );
            // Po animacji litera wraca do zwykłych stylów, więc zmiana motywu przemaluje ją jak resztę tekstu.
            anim.onfinish = () => { c.style.removeProperty('opacity'); anim.cancel(); };
        });
    };

    const io = new IntersectionObserver((entries) => {
        if (entries.some((e) => e.isIntersecting)) {
            run();
            io.disconnect();
        }
    }, { threshold, rootMargin });
    io.observe(el);
}
