<?php

declare(strict_types=1);

namespace App\Actions\Subscriptions;

use App\Models\Club;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class UpdateSubscriptionAction
{
    /**
     * Update subscription quantity based on admin count.
     *
     * @throws Throwable
     */
    public function handle(Club $club): void
    {
        try {
            $subscription = $club->subscription('default');

            if (! $subscription || ! $subscription->active()) {
                Log::warning('No active subscription found for club', [
                    'club_id' => $club->id,
                ]);

                return;
            }

            $adminCount = $club->getAdminCount();

            // Update subscription quantity
            // Base subscription includes 3 admins, charge for extras
            $quantity = max(1, $adminCount - 3);

            $subscription->updateQuantity($quantity);

            Log::info('Subscription updated for club', [
                'club_id' => $club->id,
                'admin_count' => $adminCount,
                'quantity' => $quantity,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to update subscription', [
                'club_id' => $club->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
