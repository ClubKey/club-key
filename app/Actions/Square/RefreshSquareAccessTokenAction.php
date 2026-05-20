<?php

declare(strict_types=1);

namespace App\Actions\Square;

use App\Models\SquareConnection;
use App\Services\Square\SquareOAuthService;
use Carbon\CarbonImmutable;
use Square\Exceptions\SquareException;

final readonly class RefreshSquareAccessTokenAction
{
    public function __construct(private SquareOAuthService $squareOAuth) {}

    /**
     * @throws SquareException
     */
    public function handle(SquareConnection $connection): SquareConnection
    {
        $tokenResponse = $this->squareOAuth->refreshAccessToken($connection);

        $connection->update([
            'access_token' => $tokenResponse['access_token'],
            'refresh_token' => $tokenResponse['refresh_token'],
            'access_token_expires_at' => isset($tokenResponse['expires_at'])
                ? CarbonImmutable::parse($tokenResponse['expires_at'])
                : null,
        ]);

        return $connection->fresh();
    }
}
