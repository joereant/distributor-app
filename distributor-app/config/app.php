<?php

return [
    'name' => env('APP_NAME', 'SentraX'),
    'env' => env('APP_ENV', 'local'),
    'debug' => (bool) env('APP_DEBUG', true),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Asia/Jakarta',
    'locale' => 'id',
    'fallback_locale' => 'id',
    'faker_locale' => 'id_ID',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
];
