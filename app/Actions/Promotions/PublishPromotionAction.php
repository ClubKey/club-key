<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;
use function now;

/**
 * Publish a promotion if it's due.
 */
final readonly class PublishPromotionAction
{
    public function handle(Promotion $promotion): Promotion
    {
        if ($promotion->publish_at !== null && $promotion->publish_at <= now()) {
            $promotion->update([
                'published_at' => now(),
            ]);
        }

        return $promotion;
    }
}
