<footer class="border-t-2 border-rule bg-paper">
    <div class="container-x flex flex-col gap-6 py-10 font-mono text-sm text-ink-soft sm:flex-row sm:items-start sm:justify-between">
        <div>
            <x-logo-svg class="mb-4 h-4 w-auto text-ink" />
            <p class="font-medium text-ink">{{ $site['company']['name'] }}</p>
            <p>NIP {{ $site['company']['nip'] }}</p>
            <p>{{ $site['company']['address'] }}</p>
            <p><a href="mailto:{{ $site['company']['email'] }}" class="underline underline-offset-4 hover:text-ink">{{ $site['company']['email'] }}</a></p>
        </div>
        <ul class="flex flex-wrap gap-x-6 gap-y-2">
            @foreach ($site['footer']['links'] as $link)
                <li><a href="{{ route($link['route']) }}" class="underline underline-offset-4 hover:text-ink">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</footer>
