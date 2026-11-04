<x-layouts.app :site="$site" title="Zgłoszenie przyjęte">
    <section class="section">
        <div class="container-x max-w-2xl text-center">
            <span class="mx-auto inline-flex size-16 items-center justify-center rounded-full bg-moss-pale text-moss"><x-icon name="check" class="size-8" /></span>
            <h1 class="mt-6 text-3xl font-bold tracking-tight sm:text-4xl">Dzięki, mamy Twoje zgłoszenie.</h1>
            <p class="mt-4 text-lg text-ink-soft">Przyglądamy się mu i odezwiemy się wkrótce. Potwierdź jeszcze swój adres: wysłaliśmy Ci e-mail z linkiem. Jeśli go nie widzisz, zajrzyj do spamu.</p>
            <a href="{{ route('home') }}" class="btn-secondary mt-8">Wróć na stronę główną</a>
        </div>
    </section>
</x-layouts.app>
