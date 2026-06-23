<?php

return [
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*', 'chatbot/*'],

    'allowed_methods' => ['*'],

    // 🚀 GANTI BINTANG JADI URL SPESIFIK FRONTEND KAMU (Hapus garis miring di akhir URL jika ada)
    'allowed_origins' => [
        'https://frontendapp-production-3259.up.railway.app',
        'http://localhost:5173'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 🚀 KITA NYALAKAN LAGI AGAR BROWSER TIDAK MARAH
    'supports_credentials' => true,
];
