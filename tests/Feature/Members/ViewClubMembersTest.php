<?php

declare(strict_types=1);

use App\Enums\AppRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
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
            ->has('clubUsers', 2);
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
