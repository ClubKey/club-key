<?php

declare(strict_types=1);

namespace App\Actions\Clubs;

use App\DTOs\AdminInvitatiionDto;
use App\Events\Clubs\AdminInvitationCreated;
use App\Models\Club;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreateAdminInvitationAction
{
    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(AdminInvitatiionDto $adminInvitationDto, Club $club): void
    {
        DB::transaction(function () use ($adminInvitationDto, $club): void {
            $adminInvitation = $club->adminInvitations()->create([
                'email' => $adminInvitationDto->email,
                'token' => Str::ulid(),
                'inviter_id' => Auth::id(),
                'expires_at' => now()->addDays(30),
            ]);

            AdminInvitationCreated::dispatch($adminInvitation);
        });
    }
}
