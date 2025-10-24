<?php

declare(strict_types=1);

return [

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    'path' => 'stripe',

    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),

        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
    ],

    'model' => App\Models\User::class,

    'currency' => env('CASHIER_CURRENCY', 'usd'),

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    'invoice_prefix' => env('CASHIER_INVOICE_PREFIX', 'CK'),

];
