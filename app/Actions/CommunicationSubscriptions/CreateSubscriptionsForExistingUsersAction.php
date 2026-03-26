<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\Enums\ContactType;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateSubscriptionsForExistingUsersAction
{
    public function __construct(
        private CreateSubscriptionAction $createSubscriptionAction
    ) {}

    /**
     * Execute the action.
     *
     * @throws Throwable
     */
    public function handle(): int
    {
        return DB::transaction(function (): int {
            $count = 0;
            $topics = [Topic::PROMOTIONS, Topic::NOTIFICATIONS];

            // Get all users who are members of at least one club
            $users = User::whereHas('clubs')->with('clubs')->get();

            foreach ($users as $user) {
                foreach ($user->clubs as $club) {
                    // Create email subscription if user has email
                    if ($user->email) {
                        foreach ($topics as $topic) {
                            $this->createSubscriptionAction->handle(
                                club: $club,
                                contactType: ContactType::EMAIL,
                                contactValue: $user->email,
                                topic: $topic,
                                user: $user,
                                source: 'existing_users_migration'
                            );
                            $count++;
                        }
                    }

                    // Create SMS subscription if user has phone
                    if ($user->phone) {
                        foreach ($topics as $topic) {
                            $this->createSubscriptionAction->handle(
                                club: $club,
                                contactType: ContactType::SMS,
                                contactValue: $user->phone,
                                topic: $topic,
                                user: $user,
                                source: 'existing_users_migration'
                            );
                            $count++;
                        }
                    }
                }
            }

            return $count;
        });
    }
}
