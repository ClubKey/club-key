<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Square\RefreshExpiringSquareAccessTokensAction;
use Illuminate\Console\Command;

final class RefreshSquareAccessTokens extends Command
{
    protected $signature = 'square:refresh-access-tokens';

    protected $description = 'Refresh Square OAuth access tokens that are expired or expiring soon';

    public function handle(RefreshExpiringSquareAccessTokensAction $refreshExpiringTokens): int
    {
        $result = $refreshExpiringTokens->handle();

        if ($result['refreshed'] === 0 && $result['failed'] === 0) {
            $this->info('No Square access tokens required refreshing.');

            return self::SUCCESS;
        }

        $this->info("Refreshed {$result['refreshed']} Square access token(s).");

        if ($result['failed'] > 0) {
            $this->warn("Failed to refresh {$result['failed']} Square access token(s). Check logs for details.");
        }

        return $result['failed'] > 0 ? self::FAILURE : self::SUCCESS;
    }
}
