<x-layouts.app :site="$site" title="Zapisz się do bety">
    <section class="section">
        <div class="container-x max-w-2xl">
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">{{ $content->fill($site['beta']['heading']) }}</h1>
            <p class="mt-3 text-ink-soft">{{ $site['beta']['subheading'] }}</p>
            <div class="mt-8">
                <x-email-form location="beta_page" :site="$site" :cta="$site['beta']['cta'] ?? $site['hero']['cta_primary']" :note="$content->fill($site['hero']['note'])" />
            </div>
            <p class="mt-6 text-xs leading-relaxed text-stone">
                {{ $content->fill(app(\App\Support\Content::class)->form()['rodo_clause']) }}
                <a href="{{ route('privacy') }}" class="underline">Polityka prywatności</a>.
            </p>
        </div>
    </section>
</x-layouts.app>
