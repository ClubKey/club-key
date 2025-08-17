<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Models\Club;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateClubAction
{
    public function __construct(
        private VisitQrCodeGeneratorAction $qrCodeGeneratorAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubDto $clubDto, Club $club): void
    {
        DB::transaction(function () use ($clubDto, $club): void {
            $club->update($clubDto->toArray());

            if ($club->wasChanged('slug')) {
                $this->qrCodeGeneratorAction->handle(club: $club);
                $clubLocations = $club->locations;
                foreach ($clubLocations as $location) {
                    $this->qrCodeGeneratorAction->handle(clubLocation: $location);
                }
            }
        });
    }
}
