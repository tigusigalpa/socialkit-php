<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| SocialKit API Configuration
|--------------------------------------------------------------------------
|
| This file defines the configuration used by the SocialKit Laravel
| integration to build the underlying SocialKitClient instance.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Access Key
    |--------------------------------------------------------------------------
    |
    | Your SocialKit API access key. It is sent as `x-access-key` header
    | by default. Set `key_in_query` to true to send it as a query/body
    | parameter instead (compatibility mode).
    |
    */
    'access_key' => env('SOCIALKIT_ACCESS_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    */
    'base_url' => env('SOCIALKIT_BASE_URL', 'https://api.socialkit.dev'),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Request timeout in seconds.
    |
    */
    'timeout' => (float) env('SOCIALKIT_TIMEOUT', 30),

    /*
    |--------------------------------------------------------------------------
    | Retry Attempts
    |--------------------------------------------------------------------------
    |
    | Number of automatic retries performed with exponential backoff when a
    | 429 or 5xx response is received. Default is 0 (no retries).
    | POST retry must be explicitly opted in by setting this > 0.
    |
    */
    'retry_attempts' => (int) env('SOCIALKIT_RETRY_ATTEMPTS', 0),

    /*
    |--------------------------------------------------------------------------
    | Retry Delay
    |--------------------------------------------------------------------------
    |
    | Base delay in seconds used for the exponential backoff strategy.
    |
    */
    'retry_delay' => (float) env('SOCIALKIT_RETRY_DELAY', 1.0),

    /*
    |--------------------------------------------------------------------------
    | Key In Query
    |--------------------------------------------------------------------------
    |
    | Compatibility opt-in: send the access key as a query/body parameter
    | instead of the x-access-key header. Default is false.
    |
    */
    'key_in_query' => false,

    /*
    |--------------------------------------------------------------------------
    | User Agent
    |--------------------------------------------------------------------------
    */
    'user_agent' => 'SocialKit-PHP-SDK/1.0.0',

];
