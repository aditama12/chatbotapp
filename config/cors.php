<?php

return [
    // Semua endpoint API dan auth
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*', 'chatbot/*'],

    'allowed_methods' => ['*'],

    // Gunakan env variable agar bisa ganti tanpa ubah kode.
    // Di Railway, set: FRONTEND_URL=https://frontendapp-production-3259.up.railway.app
    'allowed_origins' => array_filter([
        env('FRONTEND_URL'),
        env('APP_URL'),
    ]),

    'allowed_origins_patterns' => [],

    // Izinkan semua header termasuk Authorization (untuk Bearer Token)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // true diperlukan agar browser mengirimkan Authorization header / cookie
    // pada cross-origin request (beda domain frontend & backend)
    'supports_credentials' => true,
];
