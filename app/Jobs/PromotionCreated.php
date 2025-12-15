<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Promotions\PublishPromotionAction;
use App\Models\Promotion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

final class PromotionCreated implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Promotion $promotion
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     */
    public function handle(PublishPromotionAction $publishPromotionAction): void
    {
        $publishPromotionAction->handle($this->promotion);
    }
}
