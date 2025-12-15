<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Promotion;
use App\Models\User;
use App\Notifications\PromotionPublished;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

final class SendMemberPromotionNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly User $member,
        private readonly Promotion $promotion
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->member->notify(new PromotionPublished($this->promotion));
    }
}
