import { initWizard } from './wizard.js';
import { scramble } from './fx.js';

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

// --- Linki do zapisu (#signup, np. „Zacznij” w pasku i przyciski w cenniku): po dojechaniu kursor ląduje w polu e-mail ---
document.querySelectorAll('a[href$="#signup"]:not([data-open-beta])').forEach((a) => {
    a.addEventListener('click', () => {
        window.setTimeout(() => document.getElementById('beta-email-footer')?.focus({ preventScroll: true }), reduce ? 0 : 650);
    });
});

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

// --- Efekt tekstu: nagłówek hero (litery układają się po wejściu) ---
scramble(document.querySelector('[data-scramble-root]'), { startDelay: 150 });

// --- Filmy demo (hero: intro, sekcja WhatsApp): na telefonie wersja pionowa (…-mobile), wyżej pozioma. Źródła dopinamy tu, więc ładuje się
//     tylko jedna wersja; film w hero od razu (data-demo-eager), pozostałe dopiero gdy zbliżają się do ekranu. ---
const demoVideos = [...document.querySelectorAll('[data-demo-video]')];
const mobileDemo = window.matchMedia('(max-width: 767px)').matches;
const mountDemoVideo = (v) => {
    if (v.dataset.mounted) return;
    v.dataset.mounted = '1';
    const pick = (key) => (mobileDemo && v.dataset[`${key}Mobile`]) || v.dataset[key];
    if (pick('poster')) v.poster = pick('poster');
    [['webm', 'video/webm'], ['mp4', 'video/mp4']].forEach(([key, type]) => {
        const src = pick(key);
        if (!src) return;
        const source = document.createElement('source');
        source.src = src;
        source.type = type;
        v.appendChild(source);
    });
    v.preload = 'auto';
    v.load();
};
if (reduce) {
    // Przy ograniczonym ruchu filmy zostają na plakacie.
    demoVideos.forEach((v) => { v.removeAttribute('autoplay'); v.pause(); });
}
const nearScreen = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => entries.forEach((e) => {
        if (!e.isIntersecting) return;
        mountDemoVideo(e.target);
        nearScreen.unobserve(e.target);
    }), { rootMargin: '600px 0px' })
    : null;
demoVideos.forEach((v) => {
    if (v.hasAttribute('data-demo-eager') || !nearScreen) mountDemoVideo(v);
    else nearScreen.observe(v);
});

// --- Ankieta bety ---
const wizard = document.querySelector('[data-wizard]');
if (wizard) initWizard(wizard, track);
