<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubLocationDto;
use App\Models\ClubLocation;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateClubLocationAction
{
    public function __construct(
        private VisitQrCodeGeneratorAction $qrCodeGeneratorAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubLocationDto $dto): ClubLocation
    {
        return DB::transaction(function () use ($dto): ClubLocation {
            $clubLocation = ClubLocation::create($dto->toArray());

            $this->qrCodeGeneratorAction->handle(clubLocation: $clubLocation);

            return $clubLocation;
        });
    }
}
