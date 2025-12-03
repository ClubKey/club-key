<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Jobs\SendMemberPromotionNotification;
use App\Models\Promotion;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class SendPromotionNotificationAction
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
            foreach ($members as $index => $member) {
                try {
                    SendMemberPromotionNotification::dispatch($member, $promotion)->delay($index);
                } catch (Exception $exception) {
                    report($exception);
                }
            }
        });
    }
}
