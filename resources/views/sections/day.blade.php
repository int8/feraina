<section class="night section" id="dzien">
    <div class="wrap two">
        <div>
            <p class="eyebrow">{{ $site['day']['eyebrow'] }}</p>
            <h2 class="section-title text-paper">{{ $site['day']['heading'] }}</h2>
            <p class="lead">{{ $site['day']['lead'] }}</p>
            <ul class="rules">
                @foreach ($site['day']['rules'] as $rule)
                    <li>{{ $rule }}</li>
                @endforeach
            </ul>
        </div>
        <div class="log" id="log" data-night-log>
            <div class="log-top">
                <span>{{ $site['day']['file'] }}</span>
                <span>{{ $site['day']['range'] }}</span>
            </div>
            <ol>
                @foreach ($site['day']['entries'] as $entry)
                    <li class="night-log-entry">
                        <time>{{ $entry['time'] }}</time>
                        <span>{{ $entry['text'] }}</span>
                    </li>
                @endforeach
                <li class="night-log-entry is-visible" aria-hidden="true">
                    <time></time>
                    <span><i class="cursor" data-night-cursor></i></span>
                </li>
            </ol>
        </div>
    </div>
</section>
