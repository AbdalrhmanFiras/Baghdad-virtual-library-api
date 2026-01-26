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
        'https://abdalrhman.cupital.xyz', // رابط السيرفر
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
