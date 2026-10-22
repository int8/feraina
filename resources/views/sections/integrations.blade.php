<section id="integracje" class="section">
    <div class="wrap">
        <p class="eyebrow">{{ $site['integrations']['eyebrow'] }}</p>
        <h2 class="section-title">{{ $site['integrations']['heading'] }}</h2>
        <p class="lead">{{ $site['integrations']['lead'] }}</p>
        <div class="int">
            @foreach ($site['integrations']['groups'] as $group)
                <article>
                    <h3>{{ $group['name'] }}</h3>
                    <div class="int-list">{!! str_replace(' · ', ' <b>·</b> ', e($group['tools'])) !!}</div>
                </article>
            @endforeach
        </div>
    </div>
</section>
