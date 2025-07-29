<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\AdminInvitation;
use App\Models\User;

test('a user can resend an admin invitation', function () {
    $user = User::factory()->withClub()->create();

    $this->actingAs($user);
    $this->post(route('clubs.admin-invitations.store', $user->currentClub), [
        'email' => 'test@test.com',
        'role' => ClubRole::ADMIN->value,
    ]);

    $adminInvitation = AdminInvitation::first();

    $response = $this->post(route('clubs.admin-invitations.resend', [
        'club' => $user->currentClub,
        'adminInvitation' => $adminInvitation,
    ]));

    $response->assertSessionHas('success');
});

test('a user cannot resend an admin invitation if they do not have permission', function () {
    $user = User::factory()->withClub()->create();
    $otherUser = User::factory()->withClub()->create();

    $this->actingAs($user);
    $this->post(route('clubs.admin-invitations.store', $user->currentClub), [
        'email' => 'test@test.com',
        'role' => ClubRole::ADMIN->value,
    ]);

    $adminInvitation = AdminInvitation::first();

    $this->actingAs($otherUser);
    $response = $this->post(route('clubs.admin-invitations.resend', [
        'club' => $user->currentClub,
        'adminInvitation' => $adminInvitation,
    ]));

    $response->assertForbidden();
});
