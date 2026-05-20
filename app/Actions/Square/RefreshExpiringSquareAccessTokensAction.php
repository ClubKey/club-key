<?php

declare(strict_types=1);

namespace App\Actions\Square;

use App\Models\SquareConnection;
use App\Services\Square\SquareOAuthService;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class RefreshExpiringSquareAccessTokensAction
{
    public function __construct(
        private RefreshSquareAccessTokenAction $refreshSquareAccessToken,
        private SquareOAuthService $squareOAuth,
    ) {}

    /**
     * @return array{refreshed: int, failed: int, skipped: int}
     */
    public function handle(): array
    {
        if (! $this->squareOAuth->isConfigured()) {
            return ['refreshed' => 0, 'failed' => 0, 'skipped' => 0];
        }

        $refreshed = 0;
        $failed = 0;

        SquareConnection::query()
            ->needsRefresh()
            ->orderBy('id')
            ->each(function (SquareConnection $connection) use (&$refreshed, &$failed): void {
                try {
                    $this->refreshSquareAccessToken->handle($connection);
                    $refreshed++;
                } catch (Throwable $exception) {
                    $failed++;

                    Log::error('Failed to refresh Square access token.', [
                        'square_connection_id' => $connection->id,
                        'club_id' => $connection->club_id,
                        'message' => $exception->getMessage(),
                    ]);
                }
            });

        return [
            'refreshed' => $refreshed,
            'failed' => $failed,
            'skipped' => 0,
        ];
    }
}
