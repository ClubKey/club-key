<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Subscription Price IDs
    |--------------------------------------------------------------------------
    |
    | These are the Stripe price IDs for club subscriptions.
    | Update these with your actual Stripe price IDs from your dashboard.
    |
    */

    'prices' => [
        'club_base' => env('STRIPE_PRICE_CLUB_BASE', 'price_club_base'),
        'extra_admin' => env('STRIPE_PRICE_EXTRA_ADMIN', 'price_extra_admin'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Configuration
    |--------------------------------------------------------------------------
    |
    | Configure subscription pricing and limits.
    |
    */

    'base_price' => 5000, // $50.00 in cents
    'included_admins' => 3,
    'extra_admin_price' => 1000, // $10.00 in cents
];
