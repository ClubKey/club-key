<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Promotion;
use App\Notifications\PromotionPublished;
use Illuminate\Support\Facades\DB;

final readonly class SendPromotionEmailAction
{
    /**
     * Execute the action.
     */
    public function handle(Promotion $promotion): void
    {
        DB::transaction(function () use ($promotion): void {
            $members = $promotion->club->members;
            foreach ($members as $member) {
                $member->notify(new PromotionPublished($promotion));
                // needed for resend
                sleep(1);
            }
        });
    }
}
