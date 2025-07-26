<?php

declare(strict_types=1);

use App\Enums\ClubRole;
use App\Models\User;

test('admins of clubs can view the club administrators page', function () {
    $user = User::factory()->withClub()->create();

    $response = $this->actingAs($user)
        ->get(route('clubs.admins.index', $user->currentClub->slug));

    $response->assertStatus(200);
});

test('admins of clubs can invite users to be an admin of a club', function () {
    $user = User::factory()->withClub()->create();

    $response = $this->actingAs($user)
        ->post(route('clubs.admin-invitations.store', $user->currentClub->slug), [
            'email' => 'test@example.com',
            'role' => ClubRole::ADMIN->value,
        ]);

    $response->assertStatus(302);
    $response->assertSessionHas('success');
});
