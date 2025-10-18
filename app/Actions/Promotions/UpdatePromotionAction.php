<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\DTOs\PromotionDto;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdatePromotionAction
{
    /**
     * @throws Throwable
     */
    public function handle(Promotion $promotion, PromotionDto $data): Promotion
    {
        return DB::transaction(function () use ($promotion, $data): Promotion {
            $publishAt = $data->publishNow ? now() : $data->publishAt;

            $isActive = $data->isActive;
            if ($isActive === null) {
                $isActive = $promotion->is_active;
            }

            $promotion->update([
                'name' => $data->name,
                'description' => $data->description,
                'publish_at' => $publishAt,
                'published_at' => $data->publishedAt ?? $promotion->published_at,
                'start_at' => $data->startAt,
                'end_at' => $data->endAt,
                'is_active' => $isActive,
                'link' => $data->link,
            ]);

            return $promotion->refresh();
        });
    }
}
