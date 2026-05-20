<?php

declare(strict_types=1);

namespace App\Actions\Square;

use App\Models\Club;
use App\Services\Square\SquareOAuthService;
use Square\Exceptions\SquareException;

final readonly class DisconnectSquareConnectionAction
{
    public function __construct(private SquareOAuthService $squareOAuth) {}

    /**
     * @throws SquareException
     */
    public function handle(Club $club): void
    {
        $connection = $club->squareConnection;

        if ($connection === null) {
            return;
        }

        if ($this->squareOAuth->isConfigured()) {
            $this->squareOAuth->revokeAccessToken($connection->access_token);
        }

        $connection->delete();
    }
}
