<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Enums\ClubRole;
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

            $club->members()->attach(Auth::id(), ['role' => ClubRole::ADMIN->value]);

            $this->switchCurrentClubAction->handle(
                Auth::user(),
                $club
            );

            return $club;
        });
    }
}
