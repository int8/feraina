<x-layouts.app title="Panel">
    <section class="section">
        <div class="container-x">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-3xl font-bold tracking-tight">Zgłoszenia do bety</h1>
                <a href="{{ route('admin.export') }}" class="btn-primary">Pobierz CSV</a>
            </div>

            <dl class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach (['Wszystkie' => $total, 'Niedokończone' => $byStatus['niedokonczony'] ?? 0, 'Kompletne' => $byStatus['kompletny'] ?? 0, 'Potwierdzone' => $byStatus['potwierdzony'] ?? 0, 'Wypisane' => $byStatus['wypisany'] ?? 0] as $label => $n)
                    <div class="card">
                        <dt class="text-sm text-stone">{{ $label }}</dt>
                        <dd class="mt-1 text-3xl font-bold">{{ $n }}</dd>
                    </div>
                @endforeach
            </dl>

            <div class="mt-12 grid gap-10 lg:grid-cols-2">
                <div>
                    <h2 class="text-xl font-semibold">Ranking integracji <span class="text-sm font-normal text-stone">(kompletne i potwierdzone: {{ $countedTotal }})</span></h2>
                    <table class="mt-4 w-full text-sm">
                        <thead class="text-left text-stone"><tr><th class="py-2 pr-3">#</th><th class="py-2 pr-3">Narzędzie</th><th class="py-2 pr-3">Grupa</th><th class="py-2 text-right">Głosy</th></tr></thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($ranking as $i => $row)
                                <tr><td class="py-2 pr-3 text-stone">{{ $i + 1 }}</td><td class="py-2 pr-3 font-medium">{{ $row['name'] }}</td><td class="py-2 pr-3 text-ink-soft">{{ $row['group'] }}</td><td class="py-2 text-right tabular-nums">{{ $row['votes'] }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-10">
                    <div>
                        <h2 class="text-xl font-semibold">Typy firm</h2>
                        <table class="mt-4 w-full text-sm">
                            <tbody class="divide-y divide-line">
                                @foreach ($byType as $row)
                                    <tr><td class="py-2 pr-3 font-medium">{{ $row['label'] }}</td><td class="py-2 text-right tabular-nums">{{ $row['count'] }}</td></tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold">Niedokończone: krok przerwania</h2>
                        <table class="mt-4 w-full text-sm">
                            <tbody class="divide-y divide-line">
                                @forelse ($abandoned as $step => $n)
                                    <tr><td class="py-2 pr-3 font-medium">Krok {{ $step }}</td><td class="py-2 text-right tabular-nums">{{ $n }}</td></tr>
                                @empty
                                    <tr><td class="py-2 text-stone">Brak</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
