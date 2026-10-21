<section id="zapisz-sie" class="section bg-paper-dim">
    <div class="container-x grid gap-12 lg:grid-cols-2 lg:gap-20">
        <div>
            <h2 class="section-title">{{ $site['about']['heading'] }}</h2>
            <div class="rule-top mt-8 flex items-start gap-6 pt-6">
                @if (! empty($site['about']['founder_photo']))
                    <img src="{{ asset($site['about']['founder_photo']) }}" alt="" width="112" height="112" class="size-28 shrink-0 object-cover grayscale">
                @else
                    <span class="flex size-28 shrink-0 items-center justify-center border-2 border-rule font-mono text-xs uppercase tracking-[0.12em] text-stone" aria-hidden="true">zdjęcie</span>
                @endif
                <div>
                    <p class="text-xl font-semibold">{{ $site['about']['founder_name'] }}</p>
                    <p class="mt-2 leading-relaxed text-ink-soft">{{ $site['about']['founder_bio'] }}</p>
                    <a href="{{ $site['about']['linkedin_url'] }}" rel="me noopener" class="mt-3 inline-block font-mono text-sm underline underline-offset-4">LinkedIn ↗</a>
                </div>
            </div>
        </div>

        <div>
            <h2 class="display text-3xl sm:text-4xl">{{ $content->fill($site['beta']['heading']) }}</h2>
            <p class="mt-4 max-w-[48ch] text-ink-soft">{{ $site['beta']['subheading'] }}</p>
            <div class="mt-6">
                <x-email-form location="footer" input-id="beta-email-footer" :site="$site" :cta="$site['beta']['cta']" />
            </div>
            <p class="mt-5 max-w-[70ch] font-mono text-xs leading-relaxed text-stone">
                {{ $content->fill(app(\App\Support\Content::class)->form()['rodo_clause']) }}
                <a href="{{ route('privacy') }}" class="underline underline-offset-2">Polityka prywatności</a>.
            </p>
        </div>
    </div>
</section>
