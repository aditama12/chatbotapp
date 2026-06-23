<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Biarkan paths ini sesuai dengan yang kamu butuhkan
    'paths' => ['api/*', 'login', 'logout', 'sanctum/csrf-cookie', 'mimin/*', 'user/*'],

    // Izinkan semua metode (GET, POST, PUT, DELETE, OPTIONS)
    'allowed_methods' => ['*'],

    // UBAH INI JADI BINTANG: Izinkan semua domain frontend
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    // Izinkan semua header (terutama header 'Authorization' untuk token kita)
    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // INI YANG PALING PENTING: Wajib FALSE karena kita pakai Bearer Token, bukan Cookie
    'supports_credentials' => false,

];
