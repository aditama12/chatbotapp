<?php

return [
    // Semua endpoint API dan auth
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*', 'chatbot/*'],

    'allowed_methods' => ['*'],

    // Gunakan env variable agar bisa ganti tanpa ubah kode.
    // Di Railway, set: FRONTEND_URL=https://frontendapp-production-3259.up.railway.app
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Izinkan semua header termasuk Authorization (untuk Bearer Token)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Set false karena kita menggunakan Bearer Token, bukan cookie stateful
    'supports_credentials' => false,
];
