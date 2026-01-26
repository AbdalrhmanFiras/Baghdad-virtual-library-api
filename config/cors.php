<?php

return [
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'docs/*',
        'api-docs*',
        'login',
        'logout',
        'register',
        'user',
        'refresh',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('APP_URL', 'http://localhost:8000'),
        'http://localhost:3000',
        'http://localhost:5173',
        'https://abdalrhman.cupital.xyz',
        '*', // للتجربة فقط، ثم أزله
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
        'X-Auth-Token',
        'X-API-Key',
    ],

    'exposed_headers' => [
        'Authorization',
        'X-CSRF-TOKEN',
        'X-XSRF-TOKEN',
        'X-Auth-Token',
    ],

    'max_age' => 60 * 60 * 24, // 24 ساعة

    'supports_credentials' => true, // مهم لـ JWT
];
