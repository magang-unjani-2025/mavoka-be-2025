<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    |
    | The origins that are allowed to make cross-origin requests.
    |
    */
    'allowed_origins' => [
        'http://localhost:3000', // Ganti dengan domain Next.js Anda
        // Tambahkan domain lain jika diperlukan
    ],
    'paths' => ['api/*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    |
    | The methods that are allowed for cross-origin requests.
    |
    */
    'allowed_methods' => ['*'], // Atau ['GET', 'POST', 'PUT', 'DELETE']

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    |
    | The headers that are allowed for cross-origin requests.
    |
    */
    'allowed_headers' => ['*'], // Atau ['Content-Type', 'Accept', ...]

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    |
    | The headers that are exposed to the browser.
    |
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age
    |--------------------------------------------------------------------------
    |
    | The number of seconds that the browser can cache the preflight request.
    |
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    |
    | Whether or not the browser should send credentials (cookies, authorization
    | headers, etc.) with cross-origin requests.
    |
    */
    'supports_credentials' => false,

];