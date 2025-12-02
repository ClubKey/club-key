<?php

declare(strict_types=1);

namespace App\Actions\Promotions;

use App\Jobs\SendMemberPromotionEmail;
use App\Models\Promotion;
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
                    SendMemberPromotionEmail::dispatch($member, $promotion);
                } catch (Exception $exception) {
                    report($exception);
                }
            }
        });
    }
}
