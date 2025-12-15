<?php

declare(strict_types=1);

use App\Models\Promotion;
use App\Models\User;

test('a public promotion link can be viewed', function () {
    $promotion = Promotion::factory()->create(['link' => 'https://example.com']);

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('promotions.public-link', $promotion));

    $response->assertRedirect('https://example.com');
});

test('a public promotion link will redirect to club page if promotion does not have a link', function () {
    $promotion = Promotion::factory()->create(['link' => null]);

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get(route('promotions.public-link', $promotion));

    $response->assertRedirect(route('clubs.show', $promotion->club->slug));
});

test('a public promotion link can be viewed by a non-member', function () {
    $promotion = Promotion::factory()->create(['link' => 'https://example.com']);

    $response = $this->get(route('promotions.public-link', $promotion));

    $response->assertRedirect('https://example.com');
});
