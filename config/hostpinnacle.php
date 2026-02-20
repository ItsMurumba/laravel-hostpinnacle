<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Key
    |--------------------------------------------------------------------------
    |
    | API Key from Hostpinnacle Portal
    |
    */
    'apiKey' => getenv('HOSTPINNACLE_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Sender ID
    |--------------------------------------------------------------------------
    |
    | Sender ID from Hostpinnacle Portal
    |
    */
    'senderId' => getenv('HOSTPINNACLE_SENDER_ID'),

    /*
    |--------------------------------------------------------------------------
    | Username
    |--------------------------------------------------------------------------
    |
    | Username for logging into Hostpinnacle Portal
    |
    */
    'username' => getenv('HOSTPINNACLE_LOGIN_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Password
    |--------------------------------------------------------------------------
    |
    | Password for logging into Hostpinnacle Portal
    |
    */
    'password' => getenv('HOSTPINNACLE_LOGIN_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Base URL for Hostpinnacle API
    |
    */
    'baseUrl' => getenv('HOSTPINNACLE_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | SaaS / Multi-Account
    |--------------------------------------------------------------------------
    |
    | Enable and configure multi-account (per-tenant) Hostpinnacle credentials
    | stored in the database. When disabled, only env-based credentials are used.
    |
    */
    'saas' => [
        'enabled' => env('HOSTPINNACLE_SAAS_ENABLED', false),
        'table' => 'hostpinnacle_accounts',
        'owner_type' => env('HOSTPINNACLE_SAAS_OWNER_TYPE', 'user'),
        'owner_key' => env('HOSTPINNACLE_SAAS_OWNER_KEY', 'user_id'),
        'owner_key_type' => env('HOSTPINNACLE_SAAS_OWNER_KEY_TYPE', 'unsignedBigInteger'),
        'owner_model' => env('HOSTPINNACLE_SAAS_OWNER_MODEL', 'App\\Models\\User'),
        'encrypt_password' => env('HOSTPINNACLE_SAAS_ENCRYPT_PASSWORD', true),
        'api_routes_enabled' => env('HOSTPINNACLE_SAAS_API_ROUTES_ENABLED', true),
        'web_routes_enabled' => env('HOSTPINNACLE_SAAS_WEB_ROUTES_ENABLED', true),
        'api_prefix' => env('HOSTPINNACLE_SAAS_API_PREFIX', 'api'),
        'web_prefix' => env('HOSTPINNACLE_SAAS_WEB_PREFIX', 'hostpinnacle'),
        'api_middleware' => ['api', 'auth:sanctum'],
        'web_middleware' => ['web', 'auth'],
    ],
];
