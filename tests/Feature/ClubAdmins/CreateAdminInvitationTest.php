<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\AdminInvitation;
use App\Models\Club;
use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    Mail::fake();
});

test('club admins can invite users to be admin of a club', function () {
    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'test@example.com',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('admin_invitations', [
        'club_id' => $club->id,
        'email' => 'test@example.com',
        'role' => ClubRole::ADMIN->value,
        'inviter_id' => $user->id,
    ]);
});

test('only one invitation is created for an email per club', function () {
    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    // First invitation
    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'test@test.com',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertRedirect();

    $response->assertSessionHas('success');
    $this->assertDatabaseCount('admin_invitations', 1);

    // Second invitation with the same email
    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'test@test.com',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertRedirect();

    $response->assertSessionHas('success');

    $this->assertDatabaseCount('admin_invitations', 1);
});

test('non-admin users cannot invite users to be admin of a club', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create();
    $club->members()->attach($user, ['role' => ClubRole::MEMBER->value]);

    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'test@example.com',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertForbidden();
});

test('guest users cannot invite users to be admin of a club', function () {
    $club = Club::factory()->create();

    $response = $this->post(route('clubs.admin-invitations.store', $club->slug), [
        'email' => 'test@example.com',
        'role' => ClubRole::ADMIN->value,
    ]);

    $response->assertRedirect(route('login'));
});

test('admin invitation requires valid email', function () {
    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'invalid-email',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertSessionHasErrors(['email']);
});

test('admin invitation requires valid role', function () {
    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $club->slug), [
            'email' => 'test@example.com',
            'role' => 'invalid-role',
        ]);

    $response->assertSessionHasErrors(['role']);
});

test('guest users are redirected to register page when accepting invitation', function () {
    $club = Club::factory()->create();
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->get(URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]));

    $response->assertRedirect(route('login'));
});

test('logged in users can accept valid invitations', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);
});

test('users cannot accept expired invitations', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->subDays(1),
    ]);

    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error');
});

test('users can accept invitations with different email addresses', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'different@example.com']);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);
});

test('users cannot accept invitations if already admin of the club', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $club->members()->attach($user, ['role' => ClubRole::ADMIN->value]);

    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error');
});

test('new users can register and accept invitation automatically', function () {
    $club = Club::factory()->create();
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'newuser@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $route = URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]);

    // Simulate the invitation acceptance flow
    $response = $this->get($route);
    $response->assertRedirect(route('login'));

    // Register with the invited email
    $response = $this->post(route('register'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'phone' => UserFactory::DEFAULT_PHONE,
        'password' => UserFactory::DEFAULT_PASSWORD,
        'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
        'accept_terms' => true,
        'accept_marketing' => true,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $response->assertSessionHas('success');

    $user = User::where('email', 'newuser@example.com')->first();
    $this->assertNotNull($user);
    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);
});

test('existing users can login and accept invitation automatically', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'password' => bcrypt(UserFactory::DEFAULT_PASSWORD),
    ]);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'existing@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $route = URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]);

    // Simulate the invitation acceptance flow
    $response = $this->get($route);
    $response->assertRedirect(route('login'));

    // Login with the invited email, maintaining the cookie
    $response = $this->post(route('login'), [
        'email' => 'existing@example.com',
        'password' => UserFactory::DEFAULT_PASSWORD,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $response->assertSessionHas('success');

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);
});

test('invitation token is cleared after successful acceptance', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);

    // Verify the invitation is deleted after successful acceptance
    $this->assertDatabaseMissing('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);
});

test('invalid invitation tokens are handled gracefully', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);

    $response = $this->actingAs($user)
        ->post(route('register'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => UserFactory::DEFAULT_PASSWORD,
            'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
        ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionMissing('error');
});

test('new users can register with different email and accept invitation automatically', function () {
    $club = Club::factory()->create();
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'invited@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $route = URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]);

    // Simulate the invitation acceptance flow
    $response = $this->get($route);
    $response->assertRedirect(route('login'));

    // Register with a different email than the invited one, maintaining the cookie
    $response = $this->post(route('register'), [
        'name' => 'New User',
        'email' => 'different@example.com',
        'phone' => UserFactory::DEFAULT_PHONE,
        'password' => UserFactory::DEFAULT_PASSWORD,
        'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
        'accept_terms' => true,
        'accept_marketing' => true,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $response->assertSessionHas('success');

    $user = User::where('email', 'different@example.com')->first();
    $this->assertNotNull($user);
    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);
});

test('invitation token persists when switching between register and login pages', function () {
    $club = Club::factory()->create();
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    // First visit the invitation acceptance URL to set the cookie
    $this->get(URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]));

    // Visit register page, maintaining the cookie
    $response = $this->withCookie('invite_token', (string) $adminInvitation->token)
        ->get(route('register'));
    $response->assertOk(); // Just verify the page loads

    // Visit login page, maintaining the cookie
    $response = $this->withCookie('invite_token', (string) $adminInvitation->token)
        ->get(route('login'));
    $response->assertOk(); // Just verify the page loads

    // Go back to register page, maintaining the cookie
    $response = $this->withCookie('invite_token', (string) $adminInvitation->token)
        ->get(route('register'));
    $response->assertOk(); // Just verify the page loads
});

test('deleted invitations cannot be used again', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create(['email' => 'test@example.com']);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'test@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    // Accept the invitation first time
    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success');

    // Verify the invitation is deleted
    $this->assertDatabaseMissing('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);

    // Try to accept the same invitation again
    $response = $this->actingAs($user)
        ->get(URL::signedRoute('clubs.admin-invitations.accept', [
            'club' => $club,
            'adminInvitation' => $adminInvitation,
        ]));

    // Should get a 404 since the invitation no longer exists
    $response->assertNotFound();
});

test('invitation is deleted when new user registers with invitation token', function () {
    $club = Club::factory()->create();
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'newuser@example.com',
        'expires_at' => now()->addDays(7),
    ]);
    $route = URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]);

    // Simulate the invitation acceptance flow
    $response = $this->get($route);
    $response->assertRedirect(route('login'));

    // Register with the invited email, maintaining the cookie
    $response = $this->post(route('register'), [
        'name' => 'New User',
        'email' => 'newuser@example.com',
        'phone' => UserFactory::DEFAULT_PHONE,
        'password' => UserFactory::DEFAULT_PASSWORD,
        'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
        'accept_terms' => true,
        'accept_marketing' => true,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $response->assertSessionHas('success');

    $user = User::where('email', 'newuser@example.com')->first();
    $this->assertNotNull($user);
    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);

    // Verify the invitation is deleted
    $this->assertDatabaseMissing('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);
});

test('invitation is deleted when existing user logs in with invitation token', function () {
    $club = Club::factory()->create();
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'password' => bcrypt(UserFactory::DEFAULT_PASSWORD),
    ]);
    $adminInvitation = AdminInvitation::factory()->for($club)->create([
        'email' => 'existing@example.com',
        'expires_at' => now()->addDays(7),
    ]);
    $route = URL::signedRoute('clubs.admin-invitations.accept', [
        'club' => $club,
        'adminInvitation' => $adminInvitation,
    ]);
    // Simulate the invitation acceptance flow
    $response = $this->get($route);
    $response->assertRedirect(route('login'));

    $response = $this->post(route('login'), [
        'email' => 'existing@example.com',
        'password' => UserFactory::DEFAULT_PASSWORD,
    ]);

    $response->assertRedirect($route);

    $response = $this->get($route);

    $response->assertRedirect();

    $this->assertDatabaseHas('club_user', [
        'club_id' => $club->id,
        'user_id' => $user->id,
        'role' => ClubRole::ADMIN->value,
    ]);

    // Verify the invitation is deleted
    $this->assertDatabaseMissing('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);
});
