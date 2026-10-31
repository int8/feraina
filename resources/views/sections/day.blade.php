<section class="section" id="dzien">
    <div class="wrap">
        <div class="panel-dark">
            <div>
                <p class="eyebrow">{{ $site['day']['eyebrow'] }}</p>
                <h2 class="section-title sm" data-reveal data-reveal-to="#f5f3f2">{{ $site['day']['heading'] }}</h2>
                <p class="lead">{{ $site['day']['lead'] }}</p>
            </div>
            <div class="day-grid">
                @foreach ($site['day']['entries'] as $entry)
                    <div>
                        <time>{{ $entry['time'] }}</time>
                        <h3>{!! \App\Support\Brands::mark($entry['title']) !!}</h3>
                        <p>{!! \App\Support\Brands::mark($entry['text']) !!}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
