<?php

declare(strict_types=1);

return [
    'club' => [
        'price' => env('STRIPE_PRICE_CLUB', ''),
        'additional_admin_price' => env('STRIPE_PRICE_CLUB_ADDITIONAL_ADMIN', ''),
        'included_admins' => (int) env('CLUB_INCLUDED_ADMINS', 3),
    ],
];
