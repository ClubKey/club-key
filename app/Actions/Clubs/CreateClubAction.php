<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Enums\ClubRole;
use App\Events\AdminAddedToClub;
use App\Events\ClubCreated;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateClubAction
{
    public function __construct(
        private SwitchCurrentClubAction $switchCurrentClubAction,
        private VisitQrCodeGeneratorAction $qrCodeGeneratorAction
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubDto $dto): Club
    {
        return DB::transaction(function () use ($dto): Club {
            $club = Club::create($dto->toArray());

            $club->owner()->associate(Auth::user());

            $club->save();

            $club->members()->attach(Auth::id(), ['role' => ClubRole::ADMIN->value]);

            $this->switchCurrentClubAction->handle(
                $user = Auth::user(),
                $club
            );

            $this->qrCodeGeneratorAction->handle(club: $club);

            ClubCreated::dispatch($user, $club);
            
            // Dispatch AdminAddedToClub event for the owner
            AdminAddedToClub::dispatch($club, $user);

            return $club;
        });
    }
}
