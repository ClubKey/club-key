<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ContactType;
use App\Models\CommunicationSubscription;
use App\Models\Promotion;
use App\Notifications\PromotionPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

final class SendSubscriptionPromotionNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly CommunicationSubscription $subscription,
        private readonly Promotion $promotion,
    ) {}

    public function handle(): void
    {
        $notification = new PromotionPublished($this->promotion, $this->subscription);

        if ($this->subscription->user_id !== null) {
            $this->subscription->user->notify($notification);

            return;
        }

        match ($this->subscription->contact_type) {
            ContactType::EMAIL => Notification::route('mail', $this->subscription->contact_value)->notify($notification),
            ContactType::SMS => Notification::route('vonage', $this->subscription->contact_value)->notify($notification),
            default => null,
        };
    }
}
