<?php

declare(strict_types=1);

return [
    'opt_out_footer' => env('SMS_OPT_OUT_FOOTER', 'Reply STOP to unsubscribe.'),

    'opt_out_keywords' => [
        'stop',
        'unsubscribe',
        'cancel',
        'quit',
        'end',
    ],

    'opt_in_keywords' => [
        'start',
        'unstop',
        'subscribe',
    ],

    'rate_limit' => [
        'per_minute' => (int) env('SMS_RATE_LIMIT_PER_MINUTE', 60),
    ],

    'default_country' => env('SMS_DEFAULT_COUNTRY', 'US'),

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'monthly_limit' => 2000,
            'base_price_cents' => 5900,
            'overage_rate_cents' => 2,
        ],
        'growth' => [
            'name' => 'Growth',
            'monthly_limit' => 10000,
            'base_price_cents' => 19900,
            'overage_rate_cents' => 15,
        ],
        'scale' => [
            'name' => 'Scale',
            'monthly_limit' => 50000,
            'base_price_cents' => 69900,
            'overage_rate_cents' => 12,
        ],
    ],
];
