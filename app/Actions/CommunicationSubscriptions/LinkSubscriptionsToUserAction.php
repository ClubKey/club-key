<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\Enums\ContactType;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class LinkSubscriptionsToUserAction
{
    /**
     * Links unlinked notification subscriptions to a user by matching their email and phone.
     * Optionally scoped to a specific club.
     *
     * @return int The number of subscriptions linked.
     *
     * @throws Throwable
     */
    public function handle(User $user, ?Club $club = null): int
    {
        return DB::transaction(function () use ($user, $club): int {
            $linked = 0;
            $base = CommunicationSubscription::query()->whereNull('user_id');

            if ($club !== null) {
                $base->where('club_id', $club->id);
            }

            if ($user->email) {
                $linked += (clone $base)
                    ->forContact(ContactType::EMAIL, $user->email)
                    ->update(['user_id' => $user->id]);
            }

            if ($user->phone) {
                $linked += (clone $base)
                    ->forContact(ContactType::SMS, $user->phone)
                    ->update(['user_id' => $user->id]);
            }

            return $linked;
        });
    }
}
