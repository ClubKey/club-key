<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\ClubDto;
use App\Enums\ClubRole;
use App\Actions\Subscriptions\StartClubSubscriptionAction;
use App\Events\ClubCreated;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateClubAction
{
    public function __construct(
        private SwitchCurrentClubAction $switchCurrentClubAction,
        private VisitQrCodeGeneratorAction $qrCodeGeneratorAction,
        private StartClubSubscriptionAction $startClubSubscriptionAction
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(ClubDto $dto, string $paymentMethod): Club
    {
        $user = Auth::user();

        $club = DB::transaction(function () use ($dto, $user): Club {
            $club = Club::create($dto->toArray());

            $club->owner()->associate($user);

            $club->save();

            $club->members()->attach($user?->id, ['role' => ClubRole::ADMIN->value]);

            if ($user) {
                $this->switchCurrentClubAction->handle($user, $club);
            }

            $this->qrCodeGeneratorAction->handle(club: $club);

            return $club;
        });

        if (! $user) {
            return $club;
        }

        $this->startClubSubscriptionAction->handle($user, $club, $paymentMethod);

        ClubCreated::dispatch($user, $club);

        return $club;
    }
}
