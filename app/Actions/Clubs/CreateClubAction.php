<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Enums\ClubRole;
use App\Events\Clubs\ClubCreated;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateClubAction
{
    public function __construct(private SwitchCurrentClubAction $switchCurrentClubAction) {}

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

            ClubCreated::dispatch($user, $club);

            return $club;
        });
    }
}
