<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*'],

    'allowed_methods' => ['*'],

    // 🚀 1. GANTI BINTANG MENJADI URL FRONTEND KAMU SECARA SPESIFIK
    'allowed_origins' => [
        'https://frontendapp-production-3259.up.railway.app',
        'http://localhost:5173' // Biarkan localhost agar bisa dites di komputer
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // 🚀 2. NYALAKAN KEMBALI KREDENSIAL AGAR BROWSER TIDAK MARAH
    'supports_credentials' => true,

];
