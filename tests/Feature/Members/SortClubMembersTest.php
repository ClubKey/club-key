<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\RewardLevel;
use App\Models\User;
use App\Models\Visit;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $this->adminUser = User::factory()->withClub()->create();
    $this->club = $this->adminUser->currentClub;

    // Create reward levels
    $this->bronzeLevel = RewardLevel::factory()->create([
        'club_id' => $this->club->id,
        'name' => 'Bronze',
        'order' => 1,
    ]);

    $this->silverLevel = RewardLevel::factory()->create([
        'club_id' => $this->club->id,
        'name' => 'Silver',
        'order' => 2,
    ]);

    $this->goldLevel = RewardLevel::factory()->create([
        'club_id' => $this->club->id,
        'name' => 'Gold',
        'order' => 3,
    ]);

    // Create three users with different check-in counts
    $this->user1 = User::factory(['role' => AppRole::USER->value])->create();
    $this->user2 = User::factory(['role' => AppRole::USER->value])->create();
    $this->user3 = User::factory(['role' => AppRole::USER->value])->create();

    // Attach users to club with different visit counts and reward levels
    $this->club->members()->attach($this->user1, [
        'visits_past_30_days' => 5,
        'reward_level_id' => $this->bronzeLevel->id,
    ]);
    $this->club->members()->attach($this->user2, [
        'visits_past_30_days' => 10,
        'reward_level_id' => $this->silverLevel->id,
    ]);
    $this->club->members()->attach($this->user3, [
        'visits_past_30_days' => 15,
        'reward_level_id' => $this->goldLevel->id,
    ]);

    // Create visits with different timestamps
    Visit::factory()->create([
        'user_id' => $this->user1->id,
        'club_id' => $this->club->id,
        'created_at' => now()->subDays(10),
    ]);
    Visit::factory()->create([
        'user_id' => $this->user2->id,
        'club_id' => $this->club->id,
        'created_at' => now()->subDays(5),
    ]);
    Visit::factory()->create([
        'user_id' => $this->user3->id,
        'club_id' => $this->club->id,
        'created_at' => now()->subDays(1),
    ]);
});

pest()->group('members', 'clubs', 'club-admin', 'sorting');

test('members can be sorted by most check-ins', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'most_checkins']));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers.data', 4) // 3 members + 1 admin
            ->where('sort', 'most_checkins')
            // Verify user3 (15 visits) is first, then user2 (10), then user1 (5)
            ->where('clubUsers.data.0.user_id', $this->user3->id)
            ->where('clubUsers.data.1.user_id', $this->user2->id)
            ->where('clubUsers.data.2.user_id', $this->user1->id);
    });
});

test('members can be sorted by most recent visit', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'recent_visit']));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers.data', 4)
            ->where('sort', 'recent_visit')
            // Verify user3 (1 day ago) is first, then user2 (5 days), then user1 (10 days)
            ->where('clubUsers.data.0.user_id', $this->user3->id)
            ->where('clubUsers.data.1.user_id', $this->user2->id)
            ->where('clubUsers.data.2.user_id', $this->user1->id);
    });
});

test('members can be sorted by highest reward level', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'reward_level']));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers.data', 4)
            ->where('sort', 'reward_level')
            // Verify user3 (Gold, order 3) is first, then user2 (Silver, order 2), then user1 (Bronze, order 1)
            ->where('clubUsers.data.0.user_id', $this->user3->id)
            ->where('clubUsers.data.1.user_id', $this->user2->id)
            ->where('clubUsers.data.2.user_id', $this->user1->id);
    });
});

test('members use default sorting when no sort parameter is provided', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index'));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers.data', 4)
            ->where('sort', null);
    });
});

test('sorting preserves search functionality', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index', [
            'search' => $this->user1->email,
            'sort' => 'most_checkins',
        ]));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers.data', 1)
            ->where('search', $this->user1->email)
            ->where('sort', 'most_checkins');
    });
});
