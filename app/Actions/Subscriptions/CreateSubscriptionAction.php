<?php

declare(strict_types=1);

namespace App\Actions\Subscriptions;

use App\Models\Club;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class CreateSubscriptionAction
{
    /**
     * Create a subscription for a club.
     *
     * @throws Throwable
     */
    public function handle(Club $club, string $paymentMethod, string $priceId = 'price_club_base'): void
    {
        try {
            // Create customer in Stripe if not exists
            if (! $club->hasStripeId()) {
                $club->createAsStripeCustomer([
                    'name' => $club->name,
                    'email' => $club->email,
                ]);
            }

            // Add payment method
            $club->updateDefaultPaymentMethod($paymentMethod);

            // Create subscription
            $club->newSubscription('default', $priceId)
                ->create($paymentMethod);

            Log::info('Subscription created for club', [
                'club_id' => $club->id,
                'stripe_id' => $club->stripe_id,
            ]);
        } catch (Throwable $e) {
            Log::error('Failed to create subscription', [
                'club_id' => $club->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
