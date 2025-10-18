<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeletePromotionAction
{
    /**
     * @throws Throwable
     */
    public function handle(Promotion $promotion): void
    {
        DB::transaction(static function () use ($promotion): void {
            $promotion->delete();
        });
    }
}
