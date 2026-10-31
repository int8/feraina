@php
    // Chipy budują się z content/integrations.json (ta sama lista co w ankiecie). Dwa rzędy, każdy zdublowany dla pętli.
    // Logo przy każdej nazwie: content/brands.json (x-brand).
    $rows = [[], []];
    foreach ($integrations['groups'] as $i => $group) {
        foreach ($group['tools'] as $tool) {
            $rows[$i < 4 ? 0 : 1][] = $tool;
        }
    }
@endphp
<section class="section" id="integracje">
    <div class="wrap">
        <div class="panel-grey">
            <div>
                <p class="eyebrow">{{ $site['integrations']['eyebrow'] }}</p>
                <h2 class="section-title sm" data-reveal>{{ $site['integrations']['heading'] }}</h2>
                <p class="lead">{!! \App\Support\Brands::mark($site['integrations']['lead']) !!}</p>
            </div>
            <div>
                <div class="marquee" aria-label="Narzędzia, z którymi łączą się agenci">
                    @foreach ($rows as $r => $row)
                        <div class="marquee-row {{ $r ? 'rev' : '' }}">
                            @foreach ([false, true] as $dup)
                                @foreach ($row as $tool)
                                    <span class="chip-tool" @if ($dup) aria-hidden="true" @endif><x-brand :id="$tool['id']" />{{ $tool['name'] }}</span>
                                @endforeach
                            @endforeach
                        </div>
                    @endforeach
                </div>
                <p class="marquee-note">{!! \App\Support\Brands::mark($site['integrations']['note']) !!}</p>
            </div>
        </div>
    </div>
</section>
