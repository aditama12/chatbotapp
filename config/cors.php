<?php

return [
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['https://frontendapp-production-3259.up.railway.app'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];
