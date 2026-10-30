/* Ustawia motyw („ducha”) strony zanim załadują się style. Źródło: ?theme=nazwa (zapamiętywane) albo localStorage. */
(function () {
    var known = ['noc', 'zmierzch', 'sok', 'kartka', 'plakat'];
    try {
        var q = new URLSearchParams(location.search).get('theme');
        var t = q !== null ? q : localStorage.getItem('bw5-theme');
        if (q !== null) {
            if (known.indexOf(q) === -1) localStorage.removeItem('bw5-theme');
            else localStorage.setItem('bw5-theme', q);
        }
        if (known.indexOf(t) !== -1) document.documentElement.setAttribute('data-theme', t);
    } catch (e) {}
})();
