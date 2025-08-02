<?php

declare(strict_types=1);

namespace App\Actions\ClubRewards;

use App\Models\Reward;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteRewardAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(Reward $reward): void
    {
        DB::transaction(function () use ($reward): void {
            $reward->delete();
        });
    }
}
