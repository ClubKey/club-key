<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\Promotions\PublishPromotionAction;
use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class PublishDuePromotionsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(PublishPromotionAction $publishPromotionAction): void
    {
        Promotion::query()
            ->where('is_active', true)
            ->whereNull('published_at')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->lazyById()
            ->each(static function (Promotion $promotion) use ($publishPromotionAction): void {
                $publishPromotionAction->handle($promotion);
            });
    }
}
