<x-layouts.app :site="$site">
    @include('sections.hero')
    {{-- Kolejność: animacja 1 (hero) → zapis → możliwości → animacja 2 (WhatsApp) → reszta. Wezwanie do działania ma być wysoko. --}}
    @include('sections.beta')
    @include('sections.features')
    @include('sections.whatsapp')
    @include('sections.integrations')
    @include('sections.pricing')
    @include('sections.faq')
    @include('sections.beta-modal')
</x-layouts.app>
