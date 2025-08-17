<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Storage;
use Throwable;

final readonly class DeleteClubAction
{
    public function __construct(
        private SwitchCurrentClubAction $switchCurrentClub,
        private DeleteVisitQrCodeAction $deleteQrCodeAction,
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(Club $club, User $user): void
    {
        DB::transaction(function () use ($club, $user): void {
            $club->members()->detach();
            $club->visits()->delete();
            $club->rewardLevels()->delete();
            $club->locations()->delete();

            $users = User::with('clubs')->where('current_club_id', $club->id)->get();

            foreach ($users as $user) {
                $user->current_club_id = $user->clubs->where('id', '!=', $club->id)->first()?->id ?? null;
                $user->save();
            }

            $this->deleteQrCodeAction->handle(club: $club);

            if ($club->logo_path && Storage::disk('public')->exists($club->logo_path)) {
                Storage::disk('public')->delete($club->logo_path);
            }

            $club->delete();

            /** @var Club $clubToSwitchTo */
            $clubToSwitchTo = $user->clubs()
                ->first();

            if ($clubToSwitchTo) {
                $this->switchCurrentClub->handle($user, $clubToSwitchTo);
            }
        });
    }
}
