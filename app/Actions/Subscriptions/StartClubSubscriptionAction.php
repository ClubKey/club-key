<?php

declare(strict_types=1);

namespace App\Actions\Subscriptions;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Subscription;
use RuntimeException;
use Throwable;

final readonly class StartClubSubscriptionAction
{
    public function __construct(
        private UpdateClubSubscriptionAction $updateClubSubscriptionAction
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(User $user, Club $club, string $paymentMethod): Subscription
    {
        $priceId = config('subscriptions.club.price');

        if (! $priceId) {
            throw new RuntimeException('Club subscription price is not configured.');
        }

        return DB::transaction(function () use ($user, $club, $paymentMethod, $priceId): Subscription {
            $user->createOrGetStripeCustomer();

            $subscriptionName = sprintf('club-%d', $club->id);

            $subscription = $user->newSubscription($subscriptionName, $priceId)
                ->create($paymentMethod);

            $club->forceFill([
                'subscription_name' => $subscriptionName,
                'stripe_subscription_id' => $subscription->stripe_id,
            ])->save();

            try {
                $this->updateClubSubscriptionAction->handle($club);
            } catch (Throwable $exception) {
                Log::warning('Failed to synchronize club subscription after creation.', [
                    'club_id' => $club->id,
                    'exception' => $exception,
                ]);
            }

            return $subscription;
        });
    }
}
