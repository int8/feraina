<x-layouts.app :site="$site" title="Wypisano z listy">
    <section class="section">
        <div class="container-x max-w-2xl text-center">
            @if ($valid)
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Wypisaliśmy Cię z listy.</h1>
                <p class="mt-4 text-lg text-ink-soft">Twój adres e-mail został usunięty. Nie dostaniesz od nas więcej wiadomości.</p>
            @else
                <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Ten link jest nieaktywny.</h1>
                <p class="mt-4 text-lg text-ink-soft">Adres mógł już zostać usunięty z listy.</p>
            @endif
            <a href="{{ route('home') }}" class="btn-secondary mt-8">Wróć na stronę główną</a>
        </div>
    </section>
</x-layouts.app>
