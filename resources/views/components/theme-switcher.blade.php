@php
    // Duchy strony do porównania. Klucz = html[data-theme] (themes.css); pusty klucz = wersja bazowa.
    // Kolory próbek są w themes.css (CSP blokuje atrybuty style w HTML).
    $themes = [
        ['', 'Fabryka', 'Papier, mono, pomarańczowy punkt. Chłodny warsztat.', 'factory.com'],
        ['noc', 'Noc', 'Czerń i poświata, miękkie pigułki. Premium software.', 'linear.app'],
        ['zmierzch', 'Zmierzch', 'Serif z kursywą, ciepłe niebo, indygo. Elegancko i po ludzku.', 'mercury.com'],
        ['sok', 'Sok', 'Fiolet i żółć, grube zaokrąglenia, przechylone okno. Energia.', 'lemonsqueezy.com'],
        ['kartka', 'Kartka', 'Biel, ciężki Inter, zielona pigułka na słowie. Przyjazne narzędzie.', 'notion.com'],
        ['plakat', 'Plakat', 'Granat, grotesk 800, kobaltowy przycisk i żółty akcent. Plakat.', 'basecamp.com'],
    ];
@endphp
<div class="themer" data-themer>
    <button type="button" class="themer-btn" data-themer-toggle aria-expanded="false" aria-controls="themer-panel" aria-haspopup="listbox">
        <span class="sw-dot" data-themer-dot aria-hidden="true"></span>
        <span>Duch: <b data-themer-name>Fabryka</b></span>
    </button>
    <div id="themer-panel" class="themer-panel" data-themer-panel role="listbox" aria-label="Wybierz ducha strony" hidden>
        <header>Duch strony</header>
        @foreach ($themes as [$key, $name, $desc, $ref])
            <button type="button" class="themer-opt" role="option" data-theme-key="{{ $key }}" aria-checked="false">
                <span class="sw-card" aria-hidden="true"></span>
                <span><b>{{ $name }}</b><small>{{ $desc }} <span class="ref">wzór: {{ $ref }}</span></small></span>
            </button>
        @endforeach
        <footer>Wybór zapamiętany w tej przeglądarce. Link z <code>?theme=</code> pokazuje ten sam duch innym.</footer>
    </div>
</div>
