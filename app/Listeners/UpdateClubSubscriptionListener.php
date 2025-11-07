<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Subscriptions\UpdateSubscriptionAction;
use App\Events\AdminAddedToClub;
use App\Events\AdminRemovedFromClub;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

final readonly class UpdateClubSubscriptionListener implements ShouldQueue
{
    public function __construct(
        private UpdateSubscriptionAction $updateSubscriptionAction
    ) {}

    /**
     * Handle the event.
     */
    public function handle(AdminAddedToClub|AdminRemovedFromClub $event): void
    {
        try {
            $this->updateSubscriptionAction->handle($event->club);
        } catch (Throwable $e) {
            Log::error('Failed to update subscription when admin count changed', [
                'club_id' => $event->club->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
