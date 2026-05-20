<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Square Environment
    |--------------------------------------------------------------------------
    |
    | Supported: "sandbox", "production"
    |
    */

    'environment' => env('SQUARE_ENVIRONMENT', 'sandbox'),

    'application_id' => env('SQUARE_APPLICATION_ID'),

    'application_secret' => env('SQUARE_APPLICATION_SECRET'),

    'redirect_uri' => env('SQUARE_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes
    |--------------------------------------------------------------------------
    |
    | Space-separated list in SQUARE_OAUTH_SCOPES, or use the default below.
    |
    */

    'scopes' => array_values(array_filter(explode(
        ' ',
        (string) env(
            'SQUARE_OAUTH_SCOPES',
            'MERCHANT_PROFILE_READ CUSTOMERS_READ CUSTOMERS_WRITE PAYMENTS_READ ORDERS_READ'
        )
    ))),

    /*
    |--------------------------------------------------------------------------
    | Proactive Token Refresh
    |--------------------------------------------------------------------------
    |
    | Access tokens are refreshed when they expire within this many days.
    |
    */

    'refresh_within_days' => (int) env('SQUARE_REFRESH_WITHIN_DAYS', 7),

];
