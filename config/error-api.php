<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Error API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the external error logging API
    |
    */

    'base_url' => env('ERROR_API_BASE_URL', 'https://example.com'),
    
    'timeout' => env('ERROR_API_TIMEOUT', 15),
    
    'cache_seconds' => env('ERROR_API_CACHE_SECONDS', 60),
    
    'retry' => env('ERROR_API_RETRY', 3),
    
    'auto_refresh' => env('ERROR_AUTO_REFRESH', 30),
    
    'endpoints' => [
        'errors' => '/error-log-api/log-errors/laravel',
    ],
];
