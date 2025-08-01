<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubLocationDto;
use App\Models\ClubLocation;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateClubLocationAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubLocationDto $dto, ClubLocation $clubLocation): ClubLocation
    {
        return DB::transaction(function () use ($dto, $clubLocation): ClubLocation {
            $clubLocation->update($dto->toArray());

            return $clubLocation->fresh();
        });
    }
}
