<?php

return [
    'paths'                    => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods'          => ['*'],

    'allowed_origins'          => [
        'http://localhost:8000',
        'http://127.0.0.1:8000',
        'http://localhost:5173',
        'http://192.168.15.2:8000', // Tu IP (Acceso Web)
        'http://192.168.15.2:5173', // Tu IP (Vite/HMR)
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers'          => ['*'],

    'exposed_headers'          => [],

    'max_age'                  => 0,

    'supports_credentials'     => true, // ← MUY IMPORTANTE
];
