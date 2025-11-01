<?php

declare(strict_types=1);

use Database\Factories\UserFactory;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => UserFactory::DEFAULT_PHONE,
        'accept_terms' => true,
        'accept_marketing' => true,
        'password' => UserFactory::DEFAULT_PASSWORD,
        'password_confirmation' => UserFactory::DEFAULT_PASSWORD,
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
