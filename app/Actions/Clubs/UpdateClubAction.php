<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Models\Club;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateClubAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubDto $clubDto, Club $club): void
    {
        DB::transaction(function () use ($clubDto, $club): void {
            $club->update($clubDto->toArray());
        });
    }
}
