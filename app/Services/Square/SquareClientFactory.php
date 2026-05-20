<?php

declare(strict_types=1);

namespace App\Services\Square;

use App\Models\Club;
use App\Models\SquareConnection;
use RuntimeException;
use Square\Core\Client\RawClient;
use Square\Environments;
use Square\OAuth\OAuthClient;
use Square\SquareClient;

final class SquareClientFactory
{
    public function oauth(): OAuthClient
    {
        $options = ['baseUrl' => $this->baseUrl()];
        $rawClient = new RawClient(options: $options);

        return new OAuthClient($rawClient, $options);
    }

    public function forAccessToken(string $accessToken): SquareClient
    {
        return new SquareClient(
            token: $accessToken,
            options: ['baseUrl' => $this->baseUrl()],
        );
    }

    public function forClub(Club $club): SquareClient
    {
        $connection = $club->squareConnection;

        if (! $connection instanceof SquareConnection) {
            throw new RuntimeException('Club does not have a Square connection.');
        }

        return $this->forAccessToken($connection->access_token);
    }

    private function baseUrl(): string
    {
        return config('square.environment', 'sandbox') === 'sandbox'
            ? Environments::Sandbox->value
            : Environments::Production->value;
    }
}
