import { initWizard } from './wizard.js';
import { initPanel } from './panel.js';

document.documentElement.classList.remove('no-js');

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

// --- CTA "Zapisz się do bety": otwórz okno z polem e-mail (bez okna: link działa normalnie) ---
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

// --- Log dnia: wpisy pojawiają się po kolei po wejściu w viewport ---
const log = document.querySelector('[data-night-log]');
if (log) {
    const entries = [...log.querySelectorAll('.night-log-entry')];
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const reveal = () => {
        entries.forEach((entry, i) => {
            if (reduce) {
                entry.classList.add('is-visible');
                return;
            }
            setTimeout(() => {
                entry.classList.add('is-visible');
                if (i === entries.length - 1) log.querySelector('[data-night-cursor]')?.classList.add('night-log-cursor');
            }, 500 + i * 650);
        });
    };
    if (reduce || !('IntersectionObserver' in window)) {
        reveal();
    } else {
        const io = new IntersectionObserver((obs) => {
            if (obs.some((o) => o.isIntersecting)) {
                reveal();
                io.disconnect();
            }
        }, { threshold: 0.35 });
        io.observe(log);
    }
}

// --- Ankieta bety ---
const wizard = document.querySelector('[data-wizard]');
if (wizard) initWizard(wizard, track);

// --- Panel produktowy w hero ---
initPanel();
