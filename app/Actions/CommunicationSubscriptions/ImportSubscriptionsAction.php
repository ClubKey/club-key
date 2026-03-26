<?php

declare(strict_types=1);

namespace App\Actions\CommunicationSubscriptions;

use App\DTOs\ImportSubscriptionErrorDto;
use App\DTOs\ImportSubscriptionsResultDto;
use App\Enums\ContactType;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class ImportSubscriptionsAction
{
    public function __construct(
        private CreateSubscriptionAction $createSubscriptionAction
    ) {}

    /**
     * Execute the action.
     *
     * @param  array<int, array{contact_type: string, contact_value: string, topic: string, user_id?: int|null}>  $subscriptions
     *
     * @throws Throwable
     */
    public function handle(Club $club, array $subscriptions, string $source = 'import'): ImportSubscriptionsResultDto
    {

        return DB::transaction(function () use ($club, $subscriptions, $source): ImportSubscriptionsResultDto {
            $success = 0;
            $errors = [];

            foreach ($subscriptions as $index => $subscriptionData) {
                try {
                    $contactType = ContactType::from($subscriptionData['contact_type']);
                    $contactValue = $subscriptionData['contact_value'];
                    $topic = Topic::from($subscriptionData['topic']);
                    $userId = $subscriptionData['user_id'] ?? null;

                    $user = $userId ? User::find($userId) : null;
                    if (! $user && $contactType === ContactType::EMAIL) {
                        $user = User::whereEmail($contactValue)->first();
                    }
                    if (! $user && $contactType === ContactType::SMS) {
                        $user = User::wherePhone($contactValue)->first();
                    }

                    $this->createSubscriptionAction->handle(
                        club: $club,
                        contactType: $contactType,
                        contactValue: $contactValue,
                        topic: $topic,
                        user: $user,
                        source: $source
                    );

                    $success++;
                } catch (Throwable $e) {
                    $errors[] = new ImportSubscriptionErrorDto(
                        row: $index + 1,
                        error: $e->getMessage()
                    );
                }
            }

            return new ImportSubscriptionsResultDto(
                success: $success,
                errors: $errors
            );
        });
    }
}
