<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

pest()->group('clubs', 'view-club');

beforeEach(function () {
    $this->adminUser = User::factory()->withClub()->create();
    $this->club = $this->adminUser->currentClub;

    $this->regularUser = User::factory()->create([
        'role' => AppRole::USER,
    ]);
});

test('a guest can view a club and will see an invite', function () {
    $response = $this->get(route('clubs.show', $this->club));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('clubs/Show')
            ->has('club');
    });
});

test('a regular user can view a club and will see an invite', function () {
    $this->actingAs($this->regularUser);

    $response = $this->get(route('clubs.show', $this->club));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('clubs/Show')
            ->has('club');
    });
});

test('a user that has joined a club can view the club, and will not see an invite', function () {
    $this->actingAs($this->adminUser);

    $response = $this->get(route('clubs.show', $this->club));

    $response->assertOk();

    $response->assertInertia(function (AssertableInertia $inertia) {
        $inertia->component('clubs/Show')
            ->has('club');
    });
});
