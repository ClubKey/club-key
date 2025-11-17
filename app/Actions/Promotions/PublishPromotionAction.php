<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Actions\SendPromotionEmailAction;
use App\Models\Promotion;

use function now;

/**
 * Publish a promotion if it's due.
 */
final readonly class PublishPromotionAction
{
    public function __construct(
        private SendPromotionEmailAction $sendPromotionEmailAction,
    ) {}

    public function handle(Promotion $promotion): Promotion
    {
        if ($promotion->publish_at !== null && $promotion->publish_at <= now()) {
            $promotion->update([
                'published_at' => now(),
            ]);

            $this->sendPromotionEmailAction->handle($promotion);

        }

        return $promotion;
    }
}
