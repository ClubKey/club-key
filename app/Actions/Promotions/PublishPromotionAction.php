<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;

final readonly class PublishPromotionAction
{
    public function handle(Promotion $promotion): Promotion
    {
        if ($promotion->published_at !== null) {
            return $promotion;
        }

        $promotion->forceFill([
            'published_at' => now()->toImmutable(),
        ]);

        $promotion->save();

        return $promotion;
    }
}
