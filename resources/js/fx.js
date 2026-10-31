/**
 * Efekty tekstu.
 * - scramble(): litery nagłówka hero przez chwilę pokazują losowe znaki (na zmianę font pikselowy i zwykły), potem właściwą literę.
 *   Po ustawieniu się liter zostaje już tylko migający podkreślnik (CSS: .display .caret).
 * - reveal(): nagłówki sekcji dzielimy na litery; po wejściu w viewport każda wjeżdża od dołu, z pomarańczowego do koloru docelowego.
 * Oba efekty wyłączone przy prefers-reduced-motion.
 */

const SCRAMBLE_CHARS = '0123456789!<>-_/[]{}=+*^?#%&$@|~;:';
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const rand = (n) => (Math.random() * n) | 0;

const settle = (el) => {
    el.style.removeProperty('visibility');
    el.style.removeProperty('--sg');
    el.style.removeProperty('--sg-font');
};

export function scramble(root, { startDelay = 0, charStagger = 20, scrambleMs = 340, tickMs = 40 } = {}) {
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
