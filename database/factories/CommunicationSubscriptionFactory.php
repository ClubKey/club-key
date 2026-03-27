<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Enums\Topic;
use App\Models\Club;
use App\Models\CommunicationSubscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunicationSubscription>
 */
final class CommunicationSubscriptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'club_id' => Club::factory(),
            'user_id' => null,
            'contact_type' => ContactType::EMAIL,
            'contact_value' => $this->faker->unique()->safeEmail(),
            'topic' => $this->faker->randomElement(Topic::cases()),
            'status' => SubscriptionStatus::SUBSCRIBED,
            'subscribed_at' => now(),
            'unsubscribed_at' => null,
            'unsubscribe_reason' => null,
            'source' => 'import',
            'consent_proof' => null,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(['user_id' => $user->id]);
    }

    public function unsubscribed(): static
    {
        return $this->state([
            'status' => SubscriptionStatus::UNSUBSCRIBED,
            'subscribed_at' => null,
            'unsubscribed_at' => now(),
        ]);
    }
}
