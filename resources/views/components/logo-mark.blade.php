@props(['class' => 'h-8 w-8'])
{{--
    Sygnet: stoper z wyciętym wycinkiem pięciu minut (30°).
    Tarcza w currentColor, wycinek w kolorze tła (--logo-cut; domyślnie papier).
    Na ciemnym kaflu ustaw color:#fff i --logo-cut:#020202.
--}}
<svg xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 64 64" role="img" aria-label="Biznes w 5 minut">
    <path d="M25 2h14v7H25z" fill="currentColor"/>
    <path d="M29 9h6v6h-6z" fill="currentColor"/>
    <circle cx="32" cy="38" r="24" fill="currentColor"/>
    <path d="M32 38V17.5a20.5 20.5 0 0 1 10.25 2.75z" fill="var(--logo-cut, #f9f9f9)"/>
</svg>
