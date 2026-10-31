<x-layouts.app :site="$site" title="Zapisz się do bety">
    <section class="section">
        <div class="container-x max-w-2xl" data-wizard
             data-progress-url="{{ route('beta.progress') }}"
             data-csrf="{{ csrf_token() }}"
             data-start-step="{{ min(max((int) $lead->last_step, 1), 4) }}">

            <p class="text-sm text-stone">Zapis dla <strong class="text-ink">{{ $lead->email }}</strong></p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight sm:text-4xl">{{ $content->fill($form['heading']) }}</h1>

            <div class="wizard-progress mt-6">
                <p class="text-sm font-medium" data-progress-text aria-live="polite">Krok 1 z 4</p>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-line" role="progressbar" aria-valuemin="1" aria-valuemax="4" aria-valuenow="1" aria-label="Postęp ankiety">
                    <div class="h-full w-1/4 rounded-full bg-moss transition-[width]" data-progress-bar></div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-6 rounded-lg border border-red-700/30 bg-red-50 p-4 text-sm text-red-900" role="alert">
                    Popraw zaznaczone pola i spróbuj ponownie.
                </div>
            @endif

            <form method="post" action="{{ route('beta.finish') }}" class="mt-8" novalidate>
                @csrf

                {{-- Krok 1: typ firmy --}}
                <fieldset class="wizard-step" data-field data-required-group data-required-message="Wybierz, czym zajmuje się Twoja firma.">
                    <legend class="sr-only">Czym zajmuje się Twoja firma?</legend>
                    <h2 class="text-xl font-semibold" aria-hidden="true">Czym zajmuje się Twoja firma?</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($form['business_types'] as $type)
                            <label class="choice">
                                <input type="radio" name="business_type" value="{{ $type['id'] }}" @checked(old('business_type', $lead->business_type) === $type['id'])>
                                <span>{{ $type['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('business_type') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror

                    <div class="mt-4" data-business-other data-field hidden>
                        <label for="business_type_other" class="block text-sm font-medium">Napisz krótko, czym się zajmujesz</label>
                        <input id="business_type_other" type="text" name="business_type_other" maxlength="100" class="input mt-2 {{ $errors->has('business_type_other') ? 'input-error' : '' }}"
                               value="{{ old('business_type_other', $lead->business_type_other) }}" @if ($errors->has('business_type_other')) aria-invalid="true" @endif>
                        <p class="mt-1 text-right text-xs text-stone" data-charcount="business_type_other"></p>
                        @error('business_type_other') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                    </div>
                </fieldset>

                {{-- Krok 2: integracje --}}
                <fieldset class="wizard-step" data-field data-required-group data-min="1" data-required-message="Zaznacz przynajmniej jedną integrację." hidden>
                    <legend class="sr-only">Które integracje są dla Ciebie najważniejsze?</legend>
                    <h2 class="text-xl font-semibold" aria-hidden="true">Które integracje są dla Ciebie najważniejsze?</h2>
                    <p class="mt-1 text-ink-soft">Zaznacz przynajmniej jedną.</p>
                    @php($selected = old('integrations', $lead->integrations ?? []))
                    <div class="mt-5 space-y-6">
                        @foreach ($integrations['groups'] as $group)
                            <div>
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-stone">{{ $group['name'] }}</h3>
                                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                                    @foreach ($group['tools'] as $tool)
                                        <label class="choice py-2">
                                            <input type="checkbox" name="integrations[]" value="{{ $tool['id'] }}" @checked(in_array($tool['id'], $selected, true))>
                                            <span>{{ $tool['name'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('integrations') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                    @error('integrations.*') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                </fieldset>

                {{-- Krok 3: czego brakuje + wydatki --}}
                <fieldset class="wizard-step" hidden>
                    <legend class="sr-only">Kilka dodatkowych pytań</legend>
                    <h2 class="text-xl font-semibold" aria-hidden="true">Kilka dodatkowych pytań</h2>
                    <div class="mt-4" data-field>
                        <label for="missing_text" class="block font-medium">Czego brakuje na liście? <span class="font-normal text-stone">(opcjonalnie)</span></label>
                        <textarea id="missing_text" name="missing_text" rows="3" maxlength="300" class="input mt-2 {{ $errors->has('missing_text') ? 'input-error' : '' }}">{{ old('missing_text', $lead->missing_text) }}</textarea>
                        <p class="mt-1 text-right text-xs text-stone" data-charcount="missing_text"></p>
                        @error('missing_text') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                    </div>
                    <div class="mt-6" data-field>
                        <label for="spend_range" class="block font-medium">Ile dziś wydajesz miesięcznie na marketing lub obsługę klienta? <span class="font-normal text-stone">(opcjonalnie)</span></label>
                        <select id="spend_range" name="spend_range" class="input mt-2">
                            <option value="">Wybierz</option>
                            @foreach ($form['spend_ranges'] as $range)
                                <option value="{{ $range['id'] }}" @selected(old('spend_range', $lead->spend_range) === $range['id'])>{{ $range['label'] }}</option>
                            @endforeach
                        </select>
                        @error('spend_range') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                    </div>
                </fieldset>

                {{-- Krok 4: zgody --}}
                <fieldset class="wizard-step" hidden>
                    <legend class="sr-only">Zgody</legend>
                    <h2 class="text-xl font-semibold" aria-hidden="true">Ostatni krok: zgody</h2>
                    <div class="mt-4 space-y-3">
                        <div data-field>
                            <label class="choice">
                                <input type="checkbox" name="consent_contact" value="1" required data-required-message="Zgoda na kontakt w sprawie bety jest wymagana." @checked(old('consent_contact'))>
                                <span class="text-sm leading-relaxed">{{ $content->fill($form['consents']['contact']) }} <span class="text-stone">(wymagane)</span></span>
                            </label>
                            @error('consent_contact') <p class="field-error" role="alert" data-server-error>{{ $message }}</p> @enderror
                        </div>
                        <div data-field>
                            <label class="choice">
                                <input type="checkbox" name="consent_marketing" value="1" @checked(old('consent_marketing'))>
                                <span class="text-sm leading-relaxed">{{ $content->fill($form['consents']['marketing']) }} <span class="text-stone">(dobrowolne)</span></span>
                            </label>
                        </div>
                    </div>
                    <p class="mt-6 text-xs leading-relaxed text-stone">
                        {{ $content->fill($form['rodo_clause']) }}
                        <a href="{{ route('privacy') }}" class="underline">Polityka prywatności</a> ·
                        <a href="{{ route('terms') }}" class="underline">Regulamin bety</a>
                    </p>
                </fieldset>

                {{-- Honeypot --}}
                <div class="absolute -left-[9999px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
                    <label for="survey-www">Strona WWW firmy</label>
                    <input id="survey-www" type="text" name="firma_www" tabindex="-1" autocomplete="off" value="">
                </div>

                <div class="wizard-nav mt-8 flex items-center justify-between gap-3">
                    <a href="{{ route('home') }}" class="btn-secondary" data-wizard-close>Zamknij</a>
                    <button type="button" class="btn-secondary" data-wizard-back hidden>Wstecz</button>
                    <button type="button" class="btn-primary" data-wizard-next>Dalej</button>
                    <button type="submit" class="btn-primary" data-wizard-submit hidden>Zapisz się</button>
                </div>
                <div class="no-js-only mt-8 hidden [.no-js_&]:block">
                    <button type="submit" class="btn-primary">Zapisz się</button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.app>
