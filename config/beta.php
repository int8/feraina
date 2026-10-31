<?php

return [
    // Maksymalna liczba integracji do zaznaczenia w formularzu bety.
    'max_integrations' => 3,

    // Panel wewnętrzny (/panel). Puste hasło = panel wyłączony.
    'admin' => [
        'user' => env('ADMIN_USER'),
        'password' => env('ADMIN_PASSWORD'),
    ],

    'plausible' => [
        'domain' => env('PLAUSIBLE_DOMAIN'),
        'host' => env('PLAUSIBLE_HOST', 'plausible.io'),
    ],

    // Limity zgłoszeń z jednego IP (na minutę).
    'rate_limit' => [
        'start' => 10,
        'progress' => 60,
    ],
];
