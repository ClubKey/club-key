<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\User;
use Illuminate\Http\UploadedFile;

pest()->group('communication-subscriptions', 'subscriber-import-controller');

it('renders the import page', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $this->actingAs($user)
        ->get(route('subscribers.import'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('Subscribers/Import'));
});

it('imports subscriptions from a csv and flashes success and import_errors', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $csv = implode("\n", [
        'contact_type,contact_value,topic',
        'email,import@example.com,promotions',
    ]);

    $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csv);

    $this->withoutMiddleware()
        ->actingAs($user)
        ->post(route('subscribers.import.store'), ['file' => $file])
        ->assertRedirect()
        ->assertSessionHas('success')
        ->assertSessionHas('error_messages');
});

it('does not flash a plain array under the reserved errors session key', function () {
    $user = User::factory()->create();
    $club = Club::factory()->create();
    $club->admins()->attach($user, ['role' => 'admin']);
    $user->update(['current_club_id' => $club->id]);

    $csv = implode("\n", [
        'contact_type,contact_value,topic',
        'email,import@example.com,promotions',
    ]);

    $file = UploadedFile::fake()->createWithContent('subscribers.csv', $csv);

    $this->withoutMiddleware()
        ->actingAs($user)
        ->post(route('subscribers.import.store'), ['file' => $file]);

    // 'error_messages' holds the list; 'errors' must never be a plain array (would crash Inertia's getBags())
    expect(session('error_messages'))->toBeArray();
    expect(session('errors'))->not->toBeArray();
});
