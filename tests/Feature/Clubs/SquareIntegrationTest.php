<?php

declare(strict_types=1);

use App\Models\Club;
use App\Models\SquareConnection;
use App\Models\User;
use App\Services\Square\SquareOAuthService;

function configureSquareOAuth(): void
{
    config([
        'square.environment' => 'sandbox',
        'square.application_id' => 'sandbox-sq0idp-test',
        'square.application_secret' => 'sandbox-sq0csp-test',
        'square.redirect_uri' => 'https://club-key.test/square/oauth/callback',
    ]);
}

test('club admins can view the square integrations page', function () {
    configureSquareOAuth();

    $user = User::factory()->withClub()->create();

    $response = $this->actingAs($user)
        ->get(route('clubs.integrations.square', $user->currentClub->slug));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('clubs/Integrations')
            ->has('square')
            ->where('square.connected', false)
            ->where('square.redirect_uri', 'https://club-key.test/square/oauth/callback'));
});

test('non admins cannot view the square integrations page', function () {
    $club = Club::factory()->create();
    $member = User::factory()->create();
    $club->members()->attach($member, ['role' => 'member']);

    $response = $this->actingAs($member)
        ->get(route('clubs.integrations.square', $club->slug));

    $response->assertForbidden();
});

test('redirect uri is normalized when missing the url scheme slashes', function () {
    config([
        'square.redirect_uri' => 'https:club-key.test/square/oauth/callback',
    ]);

    expect(app(SquareOAuthService::class)->redirectUri())
        ->toBe('https://club-key.test/square/oauth/callback');
});

test('connect authorization url includes a valid redirect uri', function () {
    configureSquareOAuth();

    $url = app(SquareOAuthService::class)->authorizationUrl('test-state');

    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

    expect($query['redirect_uri'] ?? '')->toBe('https://club-key.test/square/oauth/callback');
});

test('connect redirects to square when configured', function () {
    configureSquareOAuth();

    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $response = $this->actingAs($user)
        ->get(route('clubs.integrations.square.connect', $club->slug));

    $response->assertRedirect();
    expect($response->headers->get('Location'))->toStartWith('https://connect.squareupsandbox.com/oauth2/authorize');
    expect(session('square_oauth.club_id'))->toBe($club->id);
    expect(session('square_oauth.state'))->not->toBeEmpty();
});

test('connect shows an error when square is not configured', function () {
    config([
        'square.application_id' => null,
        'square.application_secret' => null,
        'square.redirect_uri' => null,
    ]);

    $user = User::factory()->withClub()->create();

    $response = $this->actingAs($user)
        ->get(route('clubs.integrations.square.connect', $user->currentClub->slug));

    $response->assertRedirect(route('clubs.integrations.square', $user->currentClub->slug));
    $response->assertSessionHas('error');
});

test('oauth callback stores a square connection for the club', function () {
    configureSquareOAuth();

    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    $this->mock(SquareOAuthService::class, function ($mock) use ($club): void {
        $mock->shouldReceive('resolveClubIdFromCallback')
            ->once()
            ->with('expected-state')
            ->andReturn($club->id);

        $mock->shouldReceive('exchangeAuthorizationCode')
            ->once()
            ->with('authorization-code')
            ->andReturn([
                'access_token' => 'test-access-token',
                'refresh_token' => 'test-refresh-token',
                'merchant_id' => 'MERCHANT123',
                'expires_at' => now()->addDays(30)->toIso8601String(),
                'scopes' => ['MERCHANT_PROFILE_READ'],
            ]);
    });

    $response = $this->actingAs($user)
        ->withSession([
            'square_oauth.club_id' => $club->id,
            'square_oauth.state' => 'expected-state',
        ])
        ->get(route('square.oauth.callback', [
            'code' => 'authorization-code',
            'state' => 'expected-state',
        ]));

    $response->assertRedirect(route('clubs.integrations.square', $club->slug));
    $response->assertSessionHas('success');

    $connection = SquareConnection::query()->where('club_id', $club->id)->first();

    expect($connection)->not->toBeNull()
        ->and($connection->merchant_id)->toBe('MERCHANT123')
        ->and($connection->access_token)->toBe('test-access-token')
        ->and($connection->refresh_token)->toBe('test-refresh-token');
});

test('oauth callback rejects invalid state', function () {
    configureSquareOAuth();

    $user = User::factory()->withClub()->create();

    $this->mock(SquareOAuthService::class, function ($mock): void {
        $mock->shouldReceive('resolveClubIdFromCallback')
            ->once()
            ->with('wrong-state')
            ->andThrow(new RuntimeException('Invalid Square OAuth state.'));
    });

    $response = $this->actingAs($user)
        ->withSession([
            'square_oauth.club_id' => $user->currentClub->id,
            'square_oauth.state' => 'expected-state',
        ])
        ->get(route('square.oauth.callback', [
            'code' => 'authorization-code',
            'state' => 'wrong-state',
        ]));

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('error');
    expect(SquareConnection::query()->count())->toBe(0);
});

test('club admins can disconnect square', function () {
    configureSquareOAuth();

    $user = User::factory()->withClub()->create();
    $club = $user->currentClub;

    SquareConnection::factory()->for($club)->create();

    $this->mock(SquareOAuthService::class, function ($mock): void {
        $mock->shouldReceive('revokeAccessToken')->once();
        $mock->shouldReceive('isConfigured')->andReturn(true);
    });

    $response = $this->actingAs($user)
        ->delete(route('clubs.integrations.square.disconnect', $club->slug));

    $response->assertRedirect(route('clubs.integrations.square', $club->slug));
    $response->assertSessionHas('success');
    expect(SquareConnection::query()->where('club_id', $club->id)->exists())->toBeFalse();
});
