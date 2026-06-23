<?php

return [
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*', 'chatbot/*'],

    'allowed_methods' => ['*'],

    // 1. Ubah menjadi wildcard agar menerima request dari domain manapun (termasuk Railway frontend)
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 2. WAJIB diubah menjadi false agar server tidak bertengkar dengan browser
    'supports_credentials' => false,
];
