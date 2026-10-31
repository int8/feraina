/**
 * Kamera scen studio: .scene ma transform-origin 0 0, a focus() ustawia translate + scale
 * tak, by wybrany element wylądował na środku kadru. Wspólne dla /studio/intro i /studio/whatsapp.
 * Kadr to rozmiar .scene (1920×1080 poziomo, 1080×1920 w wersji na telefon: body.portrait).
 */
const scene = () => document.getElementById('scene');
const frame = () => ({ W: scene().offsetWidth, H: scene().offsetHeight });

export const portrait = () => document.body.classList.contains('portrait');

export function sceneMatrix() {
    const t = getComputedStyle(scene()).transform;
    return t === 'none' ? new DOMMatrix() : new DOMMatrix(t);
}

// Prostokąt elementu we współrzędnych sceny (niezależnie od aktualnego transformu kamery).
export function sceneRect(el) {
    const inv = sceneMatrix().inverse();
    const r = el.getBoundingClientRect();
    const a = inv.transformPoint(new DOMPoint(r.left, r.top));
    const b = inv.transformPoint(new DOMPoint(r.right, r.bottom));
    return { x: a.x, y: a.y, w: b.x - a.x, h: b.y - a.y, cx: (a.x + b.x) / 2, cy: (a.y + b.y) / 2 };
}

// dx/dy przesuwają punkt, który ląduje na środku (ujemne dx = element po prawej od środka).
// bottom: zamiast centrować w pionie, dolna krawędź elementu ląduje `bottom` px nad dolną krawędzią kadru.
export function focus(el, s, { ms = 650, dx = 0, dy = 0, bottom } = {}) {
    const r = sceneRect(el);
    const { W, H } = frame();
    const sc = scene();
    const ty = bottom === undefined ? H / 2 - (r.cy + dy) * s : H - bottom - (r.y + r.h) * s;
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = `translate(${W / 2 - (r.cx + dx) * s}px, ${ty}px) scale(${s})`;
}

// Jak focus(), ale skala nie przekroczy tej, przy której element (z marginesem pad) mieści się w kadrze.
export function focusFit(el, maxScale, { pad = 80, ...opts } = {}) {
    const r = sceneRect(el);
    const { W, H } = frame();
    const s = Math.min(maxScale, (W - pad) / r.w, (H - pad) / r.h);
    focus(el, s, opts);
    return s;
}

export function camReset(ms = 650) {
    const sc = scene();
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = 'none';
}
