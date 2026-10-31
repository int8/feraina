<section class="section" id="zapis">
    <div class="wrap">
        <div class="cta-panel">
            <h2 class="section-title" data-reveal>{{ $site['beta']['heading'] }}</h2>
            <p class="lead">{{ $site['beta']['subheading'] }}</p>
            <div class="cta-form">
                <x-email-form location="footer" inputId="beta-email-footer" :site="$site" :cta="$site['hero']['cta_primary']" :note="$site['beta']['note']" />
            </div>
        </div>
    </div>
</section>
