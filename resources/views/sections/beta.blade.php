<section class="section" id="signup">
    <div class="wrap">
        <div class="cta-panel">
            <h2 class="section-title">{{ $site['beta']['heading'] }}</h2>
            <div class="cta-form">
                <x-email-form location="footer" inputId="beta-email-footer" :site="$site" :cta="$site['hero']['cta_primary']" :note="$site['beta']['note']" />
            </div>
        </div>
    </div>
</section>
