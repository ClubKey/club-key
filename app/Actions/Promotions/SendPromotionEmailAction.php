<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Models\Promotion;
use App\Notifications\PromotionPublished;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class SendPromotionEmailAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(Promotion $promotion): void
    {
        DB::transaction(function () use ($promotion): void {
            $members = $promotion->club->members;
            foreach ($members as $member) {
                try {
                    $member->notify(new PromotionPublished($promotion));
                    // needed for resend
                    sleep(1);
                } catch (Exception $exception) {
                    report($exception);
                }
            }
        });
    }
}
