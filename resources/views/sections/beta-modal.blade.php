@php($reopen = $errors->getBag('beta')->any() && old('location') === 'modal')
<dialog class="beta-modal" id="beta-modal" aria-labelledby="beta-modal-title" @if ($reopen) data-open @endif>
    <button type="button" class="beta-modal-close" data-close-beta aria-label="Zamknij">
        <x-icon name="close" class="size-5" />
    </button>
    <h2 id="beta-modal-title" class="section-title text-3xl sm:text-4xl lg:text-4xl">{{ $site['hero']['modal_heading'] }}</h2>
    <p class="mt-3 text-ink-soft">{{ $site['hero']['modal_text'] }}</p>
    <div class="mt-6">
        <x-email-form location="modal" inputId="beta-email-modal" :site="$site" :cta="$site['hero']['cta_primary']" :note="$site['hero']['note']" />
    </div>
</dialog>
