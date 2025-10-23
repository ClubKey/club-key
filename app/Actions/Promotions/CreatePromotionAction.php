<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\DTOs\PromotionDto;
use App\Models\Club;
use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreatePromotionAction
{
    public function __construct(
        private PublishPromotionAction $publishPromotionAction,
    ) {}
    /**
     * Execute the action.
     * @throws Throwable
     */
    public function handle(PromotionDto $data, Club $club): Promotion
    {
        return DB::transaction(function () use ($data, $club): Promotion {

            $publishAt = $data->publishNow ? now()->toImmutable() : $data->publishAt;

            $promotion = $club->promotions()->create([
                'name' => $data->name,
                'description' => $data->description,
                'publish_at' => $publishAt,
                'published_at' => $data->publishedAt,
                'start_at' => $data->startAt,
                'end_at' => $data->endAt,
                'is_active' => $data->isActive ?? true,
                'link' => $data->link,
            ]);

            if ($data->publishNow) {
                $this->publishPromotionAction->handle($promotion);
            }

            return $promotion;
        });
    }
}
