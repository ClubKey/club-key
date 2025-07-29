<?php

declare(strict_types=1);

use App\Models\AdminInvitation;
use App\Models\User;

test('a user can revoke an admin invitation', function () {

    $user = User::factory()->withClub()->create();
    $adminInvitation = AdminInvitation::factory()->create([
        'club_id' => $user->currentClub->id,
        'inviter_id' => $user->id,
    ]);
    $this->actingAs($user);

    $response = $this->delete(route('clubs.admin-invitations.destroy', [
        'club' => $user->currentClub,
        'admin_invitation' => $adminInvitation,
    ]));

    $response->assertRedirect();

    $this->assertDatabaseMissing('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);
});

test('a user cannot revoke an admin invitation they do not own', function () {
    $user = User::factory()->withClub()->create();
    $otherUser = User::factory()->withClub()->create();
    $adminInvitation = AdminInvitation::factory()->create([
        'club_id' => $user->currentClub->id,
        'inviter_id' => $user->id,
    ]);

    $this->actingAs($otherUser);

    $response = $this->delete(route('clubs.admin-invitations.destroy', [
        'club' => $user->currentClub,
        'admin_invitation' => $adminInvitation,
    ]));

    $response->assertForbidden();

    $this->assertDatabaseHas('admin_invitations', [
        'id' => $adminInvitation->id,
    ]);
});
