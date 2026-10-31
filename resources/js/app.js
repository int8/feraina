import { initWizard } from './wizard.js';
import { scramble, reveal } from './fx.js';

document.documentElement.classList.remove('no-js');

const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// --- Plausible: zdarzenia z atrybutów data-plausible="nazwa" (+ data-plausible-prop-*). ---
export function track(name, props = {}) {
    if (typeof window.plausible === 'function') {
        window.plausible(name, { props });
    }
}

document.addEventListener('click', (e) => {
    const el = e.target.closest('[data-plausible]');
    if (!el) return;
    const props = {};
    for (const [key, value] of Object.entries(el.dataset)) {
        if (key.startsWith('plausibleProp')) {
            props[key.slice('plausibleProp'.length).toLowerCase()] = value;
        }
    }
    track(el.dataset.plausible, props);
});

// --- Nawigacja mobilna ---
const panel = document.querySelector('[data-nav-panel]');
const toggles = document.querySelectorAll('[data-nav-toggle]');
if (panel && toggles.length) {
    const setOpen = (open) => {
        panel.hidden = !open;
        toggles.forEach((t) => t.setAttribute('aria-expanded', String(open)));
    };
    toggles.forEach((t) => t.addEventListener('click', () => setOpen(panel.hidden)));
    panel.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => setOpen(false)));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !panel.hidden) setOpen(false);
    });
}

// --- CTA: otwórz okno z polem e-mail (bez okna: link działa normalnie) ---
const betaModal = document.getElementById('beta-modal');
if (betaModal && typeof betaModal.showModal === 'function') {
    const openBeta = () => {
        if (betaModal.open) return;
        betaModal.showModal();
        betaModal.querySelector('input[type="email"]')?.focus({ preventScroll: true });
    };
    document.querySelectorAll('[data-open-beta]').forEach((el) => {
        el.addEventListener('click', (e) => {
            e.preventDefault();
            openBeta();
        });
    });
    betaModal.querySelectorAll('[data-close-beta]').forEach((b) => b.addEventListener('click', () => betaModal.close()));
    betaModal.addEventListener('click', (e) => {
        if (e.target === betaModal) betaModal.close();
    });
    if (betaModal.hasAttribute('data-open')) openBeta();
}

// --- UTM i referrer do ukrytych pól formularza ---
const params = new URLSearchParams(window.location.search);
document.querySelectorAll('form[data-beta-start]').forEach((form) => {
    for (const key of ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content']) {
        const input = form.querySelector(`input[name="${key}"]`);
        if (input && params.get(key)) input.value = params.get(key).slice(0, 200);
    }
    const ref = form.querySelector('input[name="referrer"]');
    if (ref && !ref.value && document.referrer) ref.value = document.referrer.slice(0, 500);
    form.addEventListener('submit', () => track('beta_start', { location: form.dataset.betaStart || 'unknown' }));
});

// --- Efekty tekstu: nagłówek hero i nagłówki sekcji ---
scramble(document.querySelector('[data-scramble-root]'), { startDelay: 150 });
document.querySelectorAll('[data-reveal]').forEach((h) => reveal(h));

// --- Filmy w hero: przy ograniczonym ruchu zostają na pierwszej klatce ---
const heroVideos = [...document.querySelectorAll('[data-hero-video]')];
if (reduce) {
    heroVideos.forEach((v) => { v.removeAttribute('autoplay'); v.pause(); });
}

// --- Zakładki nad filmem (Web / WhatsApp): pokazują jeden z filmów, drugi ładuje się dopiero po wyborze ---
const heroTabs = [...document.querySelectorAll('[data-hero-tab]')];
const selectHeroTab = (tab) => {
    heroTabs.forEach((t) => {
        const on = t === tab;
        t.setAttribute('aria-selected', String(on));
        t.tabIndex = on ? 0 : -1;
    });
    document.getElementById('hero-demo')?.setAttribute('aria-labelledby', tab.id);
    heroVideos.forEach((v) => {
        const on = v.dataset.heroVideo === tab.dataset.heroTab;
        v.hidden = !on;
        if (!on) { v.pause(); return; }
        if (v.preload === 'none') { v.preload = 'auto'; v.load(); }
        if (!reduce) { v.currentTime = 0; v.play().catch(() => {}); }
    });
};
heroTabs.forEach((tab, i) => {
    tab.addEventListener('click', () => selectHeroTab(tab));
    tab.addEventListener('keydown', (e) => {
        if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
        e.preventDefault();
        const next = heroTabs[(i + (e.key === 'ArrowRight' ? 1 : heroTabs.length - 1)) % heroTabs.length];
        next.focus();
        selectHeroTab(next);
    });
});

// --- Ankieta bety ---
const wizard = document.querySelector('[data-wizard]');
if (wizard) initWizard(wizard, track);
