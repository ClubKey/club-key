<?php

declare(strict_types=1);

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;

test('password can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => UserFactory::DEFAULT_PASSWORD,
            'password' => UserFactory::UPDATE_PASSWORD,
            'password_confirmation' => UserFactory::UPDATE_PASSWORD,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/settings/password');

    expect(Hash::check(UserFactory::UPDATE_PASSWORD, $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/settings/password')
        ->put('/settings/password', [
            'current_password' => UserFactory::WRONG_PASSWORD,
            'password' => UserFactory::UPDATE_PASSWORD,
            'password_confirmation' => UserFactory::UPDATE_PASSWORD,
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect('/settings/password');
});
