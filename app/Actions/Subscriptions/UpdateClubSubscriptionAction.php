<?php

declare(strict_types=1);

namespace App\Actions\Subscriptions;

use App\Models\Club;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final readonly class UpdateClubSubscriptionAction
{
    public function handle(Club $club): void
    {
        $owner = $club->owner;

        if (! $owner || ! $club->subscription_name) {
            return;
        }

        $subscription = $owner->subscription($club->subscription_name);

        if (! $subscription) {
            return;
        }

        $additionalAdminPrice = config('subscriptions.club.additional_admin_price');

        if (! $additionalAdminPrice) {
            throw new RuntimeException('Additional admin price is not configured.');
        }

        $includedAdmins = (int) config('subscriptions.club.included_admins', 3);
        $adminCount = (int) $club->admins()->count();
        $additionalQuantity = max(0, $adminCount - $includedAdmins);

        $items = $subscription->relationLoaded('items')
            ? $subscription->items
            : $subscription->items()->get();

        if (! $items instanceof Collection) {
            $items = Collection::wrap($items);
        }

        $subscriptionItem = $items->firstWhere('stripe_price', $additionalAdminPrice);

        try {
            if ($additionalQuantity > 0) {
                if ($subscriptionItem) {
                    $subscriptionItem->updateQuantity($additionalQuantity);
                } else {
                    $subscription->addPrice($additionalAdminPrice, ['quantity' => $additionalQuantity]);
                }
            } elseif ($subscriptionItem) {
                $subscription->removePrice($additionalAdminPrice);
            }
        } catch (Throwable $exception) {
            Log::warning('Failed to update club subscription quantities.', [
                'club_id' => $club->id,
                'subscription_id' => $subscription->id,
                'additional_quantity' => $additionalQuantity,
                'exception' => $exception,
            ]);
        }
    }
}
