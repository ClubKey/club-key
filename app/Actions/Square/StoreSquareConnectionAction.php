<?php

declare(strict_types=1);

namespace App\Actions\Square;

use App\Models\Club;
use App\Models\SquareConnection;
use Carbon\CarbonImmutable;

final readonly class StoreSquareConnectionAction
{
    /**
     * @param array{
     *     access_token: string,
     *     refresh_token: string,
     *     merchant_id: string,
     *     expires_at?: string|null,
     *     scopes?: list<string>|null
     * } $tokenResponse
     */
    public function handle(Club $club, array $tokenResponse): SquareConnection
    {
        $expiresAt = isset($tokenResponse['expires_at'])
            ? CarbonImmutable::parse($tokenResponse['expires_at'])
            : null;

        return SquareConnection::query()->updateOrCreate(
            ['club_id' => $club->id],
            [
                'merchant_id' => $tokenResponse['merchant_id'],
                'access_token' => $tokenResponse['access_token'],
                'refresh_token' => $tokenResponse['refresh_token'],
                'access_token_expires_at' => $expiresAt,
                'scopes' => $tokenResponse['scopes'] ?? config('square.scopes', []),
                'connected_at' => now(),
            ],
        );
    }
}
