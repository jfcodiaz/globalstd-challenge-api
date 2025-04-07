<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://globalstd-challenge.pakodiaz.dev',
    ],

    'allowed_origins_patterns' => [
        '^http:\/\/localhost(:[0-9]+)?$',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
