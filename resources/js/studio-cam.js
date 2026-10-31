/**
 * Kamera scen studio (1920×1080): .scene ma transform-origin 0 0, a focus() ustawia translate + scale
 * tak, by wybrany element wylądował na środku kadru. Wspólne dla /studio/intro i /studio/whatsapp.
 */
const scene = () => document.getElementById('scene');

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
export function focus(el, s, { ms = 650, dx = 0, dy = 0 } = {}) {
    const r = sceneRect(el);
    const sc = scene();
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = `translate(${960 - (r.cx + dx) * s}px, ${540 - (r.cy + dy) * s}px) scale(${s})`;
}

export function camReset(ms = 650) {
    const sc = scene();
    sc.style.setProperty('--cam-ms', `${ms}ms`);
    sc.style.transform = 'none';
}
