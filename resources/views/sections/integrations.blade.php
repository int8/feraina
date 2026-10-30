@php
    // Chipy budują się z content/integrations.json (ta sama lista co w ankiecie). Dwa rzędy, każdy zdublowany dla pętli.
    $colors = ['#020202', '#ee6018', '#3d3a39', '#8a8380', '#d15010', '#4d4947', '#a49d9a'];
    $rows = [[], []];
    foreach ($integrations['groups'] as $i => $group) {
        foreach ($group['tools'] as $tool) {
            $rows[$i < 4 ? 0 : 1][] = ['name' => $tool['name'], 'c' => $colors[$i % count($colors)]];
        }
    }
@endphp
<section class="section" id="integracje">
    <div class="wrap">
        <div class="panel-grey">
            <div>
                <p class="eyebrow">{{ $site['integrations']['eyebrow'] }}</p>
                <h2 class="section-title sm" data-reveal>{{ $site['integrations']['heading'] }}</h2>
                <p class="lead">{{ $site['integrations']['lead'] }}</p>
            </div>
            <div>
                <div class="marquee" aria-label="Narzędzia, z którymi łączą się agenci">
                    @foreach ($rows as $r => $row)
                        <div class="marquee-row {{ $r ? 'rev' : '' }}">
                            @foreach ([false, true] as $dup)
                                @foreach ($row as $tool)
                                    <span class="chip-tool" style="--c: {{ $tool['c'] }}" @if ($dup) aria-hidden="true" @endif><i></i>{{ $tool['name'] }}</span>
                                @endforeach
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <p class="marquee-note">{{ $site['integrations']['note'] }}</p>
            </div>
        </div>
    </div>
</section>
