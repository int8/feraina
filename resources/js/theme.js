/**
 * Przełącznik „ducha” strony. Motywy definiuje resources/css/themes.css (html[data-theme]).
 * Wybór trafia do localStorage (klucz bw5-theme); public/theme-boot.js odtwarza go przed stylami.
 */

const KEY = 'bw5-theme';

export function initThemer(root) {
    if (!root) return;
    const btn = root.querySelector('[data-themer-toggle]');
    const panel = root.querySelector('[data-themer-panel]');
    const opts = [...root.querySelectorAll('[data-theme-key]')];
    if (!btn || !panel || !opts.length) return;

    const current = () => document.documentElement.getAttribute('data-theme') || '';

    const paint = () => {
        const key = current();
        opts.forEach((o) => o.setAttribute('aria-checked', String(o.dataset.themeKey === key)));
        const active = opts.find((o) => o.dataset.themeKey === key) || opts[0];
        btn.querySelector('[data-themer-name]').textContent = active.querySelector('b').textContent;
    };

    const apply = (key) => {
        if (key) document.documentElement.setAttribute('data-theme', key);
        else document.documentElement.removeAttribute('data-theme');
        try {
            if (key) localStorage.setItem(KEY, key);
            else localStorage.removeItem(KEY);
        } catch {}
        // Adres z ?theme= do podesłania komuś.
        const url = new URL(location.href);
        if (key) url.searchParams.set('theme', key);
        else url.searchParams.delete('theme');
        history.replaceState(null, '', url);
        paint();
    };

    const setOpen = (open) => {
        panel.hidden = !open;
        btn.setAttribute('aria-expanded', String(open));
    };

    btn.addEventListener('click', () => setOpen(panel.hidden));
    opts.forEach((o) => o.addEventListener('click', () => { apply(o.dataset.themeKey); }));
    document.addEventListener('click', (e) => { if (!root.contains(e.target)) setOpen(false); });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.hidden) { setOpen(false); btn.focus(); }
    });

    paint();
}
