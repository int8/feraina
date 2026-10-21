<x-layouts.app :site="$site" title="Zapis potwierdzony">
    <section class="section">
        <div class="container-x max-w-2xl text-center">
            @if ($valid)
                <span class="mx-auto inline-flex size-16 items-center justify-center rounded-full bg-moss-pale text-moss"><x-icon name="check" class="size-8" /></span>
                <h1 class="mt-6 text-3xl font-bold tracking-tight sm:text-4xl">Dziękujemy, zapis potwierdzony.</h1>
                <p class="mt-4 text-lg text-ink-soft">Odezwiemy się, gdy ruszy beta.</p>
            @else
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Ten link jest nieaktywny.</h1>
                <p class="mt-4 text-lg text-ink-soft">Zapis mógł już zostać potwierdzony albo link wygasł. Możesz zapisać się ponownie.</p>
            @endif
            <a href="{{ route('home') }}" class="btn-secondary mt-8">Wróć na stronę główną</a>
        </div>
    </section>
</x-layouts.app>
