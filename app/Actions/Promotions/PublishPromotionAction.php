<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;

final readonly class PublishPromotionAction
{
    public function handle(Promotion $promotion): Promotion
    {
        $promotion->update([
            'published_at' => now(),
        ]);

        return $promotion;
    }
}
