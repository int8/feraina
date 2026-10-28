@props(['location', 'inputId' => 'beta-email', 'cta', 'note' => null, 'site' => null])
@php($site ??= $content->site())
@php($errors = $errors->getBag('beta'))
<form method="post" action="{{ route('beta.start') }}" data-beta-start="{{ $location }}" class="w-full" novalidate>
    @csrf
    <div class="flex flex-col sm:flex-row sm:items-start" data-field>
        <div class="flex-1">
            <label for="{{ $inputId }}" class="sr-only">Adres e-mail</label>
            <input id="{{ $inputId }}" type="email" name="email" inputmode="email" autocomplete="email" required
                   value="{{ old('email') }}" placeholder="{{ $site['hero']['email_placeholder'] }}"
                   class="input sm:border-r-0 {{ $errors->has('email') ? 'input-error' : '' }}"
                   @if ($errors->has('email')) aria-invalid="true" aria-describedby="{{ $inputId }}-error" @endif>
        </div>
        <button type="submit" class="btn-primary shrink-0 border-2 border-ink">{{ $cta }}</button>
    </div>
    @if ($errors->has('email'))
        <p id="{{ $inputId }}-error" class="field-error" role="alert">{{ $errors->first('email') }}</p>
    @endif

    {{-- Honeypot: ukryte pole, którego człowiek nie wypełni --}}
    <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
        <label for="{{ $inputId }}-www">Strona WWW firmy</label>
        <input id="{{ $inputId }}-www" type="text" name="firma_www" tabindex="-1" autocomplete="off" value="">
    </div>

    @foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'] as $utm)
        <input type="hidden" name="{{ $utm }}" value="{{ request()->query($utm) }}">
    @endforeach
    <input type="hidden" name="referrer" value="">
    <input type="hidden" name="location" value="{{ $location }}">

    @if ($note)
        <p class="mt-3 font-mono text-[13px] text-stone">{{ $note }}</p>
    @endif
</form>
