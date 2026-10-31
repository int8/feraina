/**
 * Ankieta zapisu do bety: kroki, pasek postępu, „Wstecz",
 * zapis postępu po każdym kroku (żeby nie tracić porzuconych zapisów).
 */
export function initWizard(root, track = () => {}) {
    const form = root.querySelector('form');
    const steps = [...root.querySelectorAll('.wizard-step')];
    const progressText = root.querySelector('[data-progress-text]');
    const progressBar = root.querySelector('[data-progress-bar]');
    const closeBtn = root.querySelector('[data-wizard-close]');
    const backBtn = root.querySelector('[data-wizard-back]');
    const nextBtn = root.querySelector('[data-wizard-next]');
    const submitBtn = root.querySelector('[data-wizard-submit]');
    const progressUrl = root.dataset.progressUrl;
    const csrf = root.dataset.csrf;
    let current = clamp(Number(root.dataset.startStep || 1) - 1, 0, steps.length - 1);

    // Jeśli serwer zwrócił błędy walidacji, otwórz pierwszy krok z błędem.
    const firstErrorStep = steps.findIndex((s) => s.querySelector('[data-server-error]'));
    if (firstErrorStep >= 0) current = firstErrorStep;

    const boxes = [...form.querySelectorAll('input[name="integrations[]"]')];

    // --- „Inne” odsłania pole tekstowe ---
    const otherWrap = form.querySelector('[data-business-other]');
    const otherInput = otherWrap?.querySelector('input');
    const refreshOther = () => {
        const isOther = form.querySelector('input[name="business_type"]:checked')?.value === 'inne';
        if (otherWrap) otherWrap.hidden = !isOther;
        if (otherInput) otherInput.required = isOther;
    };
    form.querySelectorAll('input[name="business_type"]').forEach((r) => r.addEventListener('change', refreshOther));
    refreshOther();

    // --- Liczniki znaków ---
    form.querySelectorAll('[data-charcount]').forEach((el) => {
        const field = form.querySelector(`#${el.dataset.charcount}`);
        if (!field) return;
        const update = () => (el.textContent = `${field.value.length} / ${field.maxLength}`);
        field.addEventListener('input', update);
        update();
    });

    // --- Walidacja kroku po stronie klienta ---
    function validateStep(i) {
        const step = steps[i];
        clearErrors(step);
        let ok = true;

        const groups = [step, ...step.querySelectorAll('[data-required-group]')].filter((g) => g.matches('[data-required-group]'));
        groups.forEach((group) => {
            const inputs = [...group.querySelectorAll('input[type="radio"], input[type="checkbox"]')];
            const minSel = Number(group.dataset.min || 1);
            const n = inputs.filter((x) => x.checked).length;
            if (n < minSel) {
                showError(group, group.dataset.requiredMessage || 'To pole jest wymagane.');
                ok = false;
            }
        });

        step.querySelectorAll('input[required]:not([type="radio"]):not([type="checkbox"]), textarea[required]').forEach((el) => {
            if (el.closest('[hidden]')) return;
            if (!el.value.trim()) {
                showError(el, 'To pole jest wymagane.');
                ok = false;
            }
        });

        step.querySelectorAll('input[type="checkbox"][required]').forEach((el) => {
            if (!el.checked) {
                showError(el.closest('[data-field]') || el, el.dataset.requiredMessage || 'To pole jest wymagane.');
                ok = false;
            }
        });

        if (!ok) step.querySelector('[aria-invalid="true"], .field-error')?.scrollIntoView({ block: 'center' });
        return ok;
    }

    function showError(target, message) {
        const field = target.closest('[data-field]') || target;
        let msg = field.querySelector('.field-error[data-client-error]');
        if (!msg) {
            msg = document.createElement('p');
            msg.className = 'field-error';
            msg.dataset.clientError = '';
            msg.setAttribute('role', 'alert');
            field.appendChild(msg);
        }
        msg.textContent = message;
        field.querySelectorAll('input, textarea, select').forEach((i) => i.setAttribute('aria-invalid', 'true'));
    }

    function clearErrors(step) {
        step.querySelectorAll('[data-client-error]').forEach((m) => m.remove());
        step.querySelectorAll('[aria-invalid]').forEach((i) => i.removeAttribute('aria-invalid'));
    }

    // --- Zapis postępu ---
    async function saveProgress(reachedStep) {
        if (!progressUrl) return;
        const data = new FormData(form);
        const payload = {
            last_step: reachedStep,
            business_type: data.get('business_type') || null,
            business_type_other: data.get('business_type_other') || null,
            integrations: data.getAll('integrations[]'),
            missing_text: data.get('missing_text') || null,
            spend_range: data.get('spend_range') || null,
        };
        try {
            const res = await fetch(progressUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify(payload),
                keepalive: true,
            });
            if (res.status === 419) {
                const body = await res.json().catch(() => ({}));
                if (body.redirect) window.location.href = body.redirect;
            }
        } catch {
            // Brak sieci nie blokuje użytkownika; pełny zapis i tak odbywa się na końcu.
        }
    }

    // --- Pokazywanie kroków ---
    function show(i, focus = true) {
        current = clamp(i, 0, steps.length - 1);
        steps.forEach((s, idx) => {
            s.hidden = idx !== current;
        });
        const last = current === steps.length - 1;
        const first = current === 0;
        if (closeBtn) closeBtn.hidden = !first;
        backBtn.hidden = first;
        nextBtn.hidden = last;
        submitBtn.hidden = !last;
        if (progressText) progressText.textContent = `Krok ${current + 1} z ${steps.length}`;
        if (progressBar) {
            progressBar.style.width = `${Math.round(((current + 1) / steps.length) * 100)}%`;
            progressBar.parentElement?.setAttribute('aria-valuenow', String(current + 1));
        }
        if (focus) {
            const heading = steps[current].querySelector('h2');
            heading?.setAttribute('tabindex', '-1');
            heading?.focus();
            window.scrollTo({ top: 0, behavior: 'auto' });
        }
    }

    function next() {
        if (!validateStep(current)) return;
        const reached = current + 2; // numer kroku, na który użytkownik przechodzi (1-based)
        saveProgress(reached);
        track('beta_step', { step: String(reached) });
        show(current + 1);
    }

    nextBtn.addEventListener('click', next);
    backBtn.addEventListener('click', () => show(current - 1));

    form.addEventListener('submit', (e) => {
        if (current < steps.length - 1) {
            e.preventDefault();
            next();
            return;
        }
        if (!validateStep(current)) {
            e.preventDefault();
            return;
        }
        // Przed wysyłką odblokuj checkboxy, żeby zaznaczone na pewno poszły w formularzu.
        boxes.forEach((b) => (b.disabled = false));
        track('beta_complete');
    });

    // Enter w polu tekstowym = „Dalej”, nie wysyłka całości.
    form.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && e.target.matches('input') && current < steps.length - 1) {
            e.preventDefault();
            next();
        }
    });

    show(current, false);
}

function clamp(n, min, max) {
    return Math.min(Math.max(n, min), max);
}
