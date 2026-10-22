<section class="section" style="background: var(--color-paper-dim)">
    <div class="wrap">
        <div class="center">
            <p class="eyebrow">{{ $site['why']['eyebrow'] }}</p>
            <h2 class="section-title">{{ $site['why']['heading'] }}</h2>
        </div>
        <div class="why-grid">
            @foreach ($site['why']['items'] as $item)
                <div>
                    <div class="why-icon">{{ $item['icon'] }}</div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
