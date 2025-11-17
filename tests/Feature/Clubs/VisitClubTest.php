<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;
use Database\Factories\UserFactory;

pest()->group('clubs', 'visits');

test('a club can be visited by any user', function () {
    $club = Club::factory()->create();

    $user = User::factory()->create();
    $route = URL::signedRoute('clubs.visit', ['club' => $club]);
    $response = $this->actingAs($user)->get($route);

    $response->assertRedirect(route('clubs.show', $club->slug));

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('visits', [
        'club_id' => $club->id,
        'user_id' => $user->id,
    ]);
});

test('when a club is first visited by a user they become a member of the club', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create();

    $route = URL::signedRoute('clubs.visit', ['club' => $club]);
    $response = $this->actingAs($user)->get($route);
    $response->assertRedirect(route('clubs.show', $club->slug));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('visits', [
        'club_id' => $club->id,
        'user_id' => $user->id,
    ]);

    expect($user->belongsToClub($club))->toBeTrue();
});

test('when a guest visits a club, they will be redirected to login, then after logging in will register a visit', function () {
    $club = Club::factory()->create();

    $route = URL::signedRoute('clubs.visit', ['club' => $club]);

    $response = $this->get($route);

    $response->assertRedirect(route('login'));

    $response = $this->post(route('register'), [
        'name' => 'John Doe',
        'email' => 'test@test.com',
        'phone' => UserFactory::DEFAULT_PHONE,
        'password' => UserFactory::DEFAULT_PASSWORD,
        'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
        'accept_terms' => true,
        'accept_marketing' => true,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $user = User::whereEmail('test@test.com')->first();

    $response->assertRedirect(route('clubs.show', $club->slug));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('visits', [
        'club_id' => $club->id,
        'user_id' => $user?->id,
    ]);
});
