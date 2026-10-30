<section class="section" id="zasady">
    <div class="wrap">
        <div class="sec-head center">
            <h2 class="section-title" data-reveal>{{ $site['principles']['heading'] }}</h2>
            <p class="lead">{{ $site['principles']['lead'] }}</p>
        </div>
        <div class="cards">
            @foreach ($site['principles']['items'] as $item)
                <article class="card-p">
                    <div class="k"><b>{{ $item['k'] }}</b><span>{{ $item['label'] }}</span></div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
