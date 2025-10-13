<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Enums\ClubRole;
use App\Models\RewardLevel;
use App\Models\User;
use App\Models\Visit;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

    $this->adminUser = User::factory()->withClub()->create();

    $this->regularUser = User::factory([
        'role' => AppRole::USER->value,
    ])->create();

    $this->club = $this->adminUser->currentClub;

    $this->club->members()->attach($this->regularUser);
});

pest()->group('members', 'clubs', 'club-admin');

test('a club admin can view the members in their club', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.index'));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Index')
            ->has('clubUsers', 2)
            ->where('clubUsers.0.total_visits', 0)
            ->where('dataTable.state.search', '')
            ->where('dataTable.state.sort.key', 'new_members')
            ->where('dataTable.state.sort.direction', 'desc');
    });
});

test('a regular user cannot view the members in a club', function () {
    $response = $this->actingAs($this->regularUser)
        ->get(route('members.index'));

    $response->assertForbidden();
});

test('a guest cannot view the members in a club', function () {
    $response = $this->get(route('members.index'));

    $response->assertRedirect(route('login'));
});

test('an admin of a club can view the member details of a user in their club', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('members.show', ['member' => $this->regularUser]));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('club-users/Show')
            ->has('user');
    });
});

test('club members can be searched by name email or phone', function () {
    $memberWithName = User::factory()->create([
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
        'phone' => '+15095550001',
    ]);
    $memberWithEmail = User::factory()->create([
        'name' => 'Brandon Smith',
        'email' => 'brandon.unique@example.com',
        'phone' => '+15095550002',
    ]);
    $memberWithPhone = User::factory()->create([
        'name' => 'Caroline Lee',
        'email' => 'caroline@example.com',
        'phone' => '+15095551234',
    ]);

    $this->club->members()->attach($memberWithName);
    $this->club->members()->attach($memberWithEmail);
    $this->club->members()->attach($memberWithPhone);

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['search' => 'alice']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->has('clubUsers', 1)
            ->where('clubUsers.0.user.name', 'Alice Johnson')
            ->where('dataTable.state.search', 'alice'));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['search' => 'brandon.unique@example.com']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->has('clubUsers', 1)
            ->where('clubUsers.0.user.email', 'brandon.unique@example.com'));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['search' => '551234']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->has('clubUsers', 1)
            ->where('clubUsers.0.user.name', 'Caroline Lee'));
});

test('club members can be filtered by role and reward level', function () {
    $secondAdmin = User::factory()->create();
    $member = User::factory()->create();

    $this->club->members()->attach($secondAdmin, ['role' => ClubRole::ADMIN->value]);
    $this->club->members()->attach($member, ['role' => ClubRole::MEMBER->value]);

    $rewardLevel = RewardLevel::factory()->create([
        'club_id' => $this->club->id,
    ]);

    $this->club->clubUsers()
        ->where('user_id', $member->id)
        ->update(['reward_level_id' => $rewardLevel->id]);

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['filters' => ['role' => [ClubRole::ADMIN->value]]]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->has('clubUsers', 2)
            ->where('clubUsers.0.role', ClubRole::ADMIN->value)
            ->where('clubUsers.1.role', ClubRole::ADMIN->value));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['filters' => ['reward_level_id' => [(string) $rewardLevel->id]]]))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->has('clubUsers', 1)
            ->where('clubUsers.0.user_id', $member->id));
});

test('club members can be sorted by visits and join date', function () {
    $firstMember = User::factory()->create(['name' => 'Aaron Member']);
    $secondMember = User::factory()->create(['name' => 'Zelda Member']);

    $this->club->members()->attach($firstMember);
    $this->club->members()->attach($secondMember);

    Visit::factory()->count(5)->forClub($this->club)->forUser($firstMember)->create([
        'created_at' => now()->subDays(10),
    ]);

    Visit::factory()->count(3)->forClub($this->club)->forUser($secondMember)->create([
        'created_at' => now()->subDays(2),
    ]);

    Visit::factory()->count(4)->forClub($this->club)->forUser($firstMember)->create([
        'created_at' => now()->subDays(3),
    ]);

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'most_visits']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->where('clubUsers.0.user_id', $firstMember->id));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'most_visits_last_week']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->where('clubUsers.0.user_id', $firstMember->id));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'name']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->where('clubUsers.0.user.name', 'Aaron Member'));

    $this->actingAs($this->adminUser)
        ->get(route('members.index', ['sort' => 'old']))
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('club-users/Index')
            ->where('clubUsers.0.user_id', $this->adminUser->id));
});
