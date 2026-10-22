<section class="night section center" id="zapis">
    <div class="wrap">
        <h2 class="section-title text-paper">{{ $site['beta']['heading'] }}</h2>
        <p class="lead">{{ $site['beta']['subheading'] }}</p>
        <div class="beta-form">
            <x-email-form location="footer" inputId="beta-email-footer" :site="$site" :cta="$site['hero']['cta_primary']" />
        </div>
    </div>
</section>
