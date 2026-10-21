<section id="cennik" class="section bg-paper-dim">
    <div class="container-x grid gap-10 lg:grid-cols-[1fr_2fr] lg:gap-16">
        <h2 class="section-title">{{ $site['pricing']['heading'] }}</h2>
        <div class="min-w-0 border-2 border-rule bg-paper">
            <div class="flex flex-wrap items-baseline justify-between gap-4 border-b-2 border-rule px-6 py-5 sm:px-8">
                <p class="text-lg font-semibold">{{ $site['pricing']['plan_name'] }}</p>
                <p class="flex items-baseline gap-2">
                    <span class="display text-4xl sm:text-6xl">{{ $site['pricing']['price'] }}</span>
                    <span class="font-mono text-sm text-stone">/ {{ $site['pricing']['period'] }}</span>
                </p>
            </div>
            <dl class="min-w-0 px-6 font-mono text-sm wrap-anywhere sm:px-8">
                <div class="flex justify-between gap-6 border-b border-line py-3"><dt class="text-stone">Podatek</dt><dd>{{ $site['pricing']['vat_note'] }}</dd></div>
                <div class="flex justify-between gap-6 border-b border-line py-3"><dt class="text-stone">Rozliczenie</dt><dd>{{ $site['pricing']['invoice_note'] }}</dd></div>
                <div class="flex justify-between gap-6 border-b border-line py-3"><dt class="text-stone">Umowa</dt><dd class="text-right">{{ $site['pricing']['cancel_note'] }}</dd></div>
                @foreach ($site['pricing']['features'] as $feature)
                    <div class="flex justify-between gap-6 border-b border-line py-3"><dt class="text-stone">W cenie</dt><dd class="text-right">{{ $feature }}</dd></div>
                @endforeach
                <div class="flex items-center justify-between gap-6 py-4"><dt class="text-stone">Dla uczestników bety</dt><dd class="text-right font-medium text-stamp">{{ $site['pricing']['beta_label'] }}</dd></div>
            </dl>
            <div class="border-t-2 border-rule px-6 py-6 sm:px-8">
                <p class="font-medium">{{ $site['pricing']['comparison'] }}</p>
                <a href="#zapisz-sie" class="btn-primary mt-5 w-full sm:w-auto" data-scroll-to-email="beta-email-footer" data-plausible="cta_click" data-plausible-prop-location="pricing">{{ $site['pricing']['cta'] }}</a>
            </div>
        </div>
    </div>
</section>
