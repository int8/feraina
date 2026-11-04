/**
 * Efekty tekstu.
 * - scramble(): litery nagłówka hero przez chwilę pokazują losowe znaki (na zmianę font pikselowy i zwykły), potem właściwą literę.
 *   Po ustawieniu się liter zostaje już tylko migający podkreślnik (CSS: .display .caret).
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
