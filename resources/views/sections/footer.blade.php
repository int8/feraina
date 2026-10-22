<footer class="border-t-2 border-rule bg-paper">
    <div class="container-x flex items-center justify-between gap-6 py-8 font-mono text-sm text-ink-soft">
        <x-logo-svg class="h-4 w-auto text-ink" />
        <ul class="flex flex-wrap gap-x-6 gap-y-2">
            @foreach ($site['footer']['links'] as $link)
                <li><a href="{{ route($link['route']) }}" class="underline underline-offset-4 hover:text-ink">{{ $link['label'] }}</a></li>
            @endforeach
        </ul>
    </div>
</footer>
