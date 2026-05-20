<?php

declare(strict_types=1);

use App\Actions\Square\RefreshExpiringSquareAccessTokensAction;
use App\Actions\Square\RefreshSquareAccessTokenAction;
use App\Models\Club;
use App\Models\SquareConnection;
use App\Services\Square\SquareOAuthService;
use Illuminate\Support\Facades\Artisan;

function configureSquareOAuthForRefresh(): void
{
    config([
        'square.environment' => 'sandbox',
        'square.application_id' => 'sandbox-sq0idp-test',
        'square.application_secret' => 'sandbox-sq0csp-test',
        'square.redirect_uri' => 'https://club-key.test/square/oauth/callback',
        'square.refresh_within_days' => 7,
    ]);
}

test('square connection needs refresh when expiring within configured days', function () {
    config(['square.refresh_within_days' => 7]);

    $connection = SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(3),
    ]);

    expect($connection->needsRefresh())->toBeTrue();
});

test('square connection does not need refresh when expiry is far in the future', function () {
    config(['square.refresh_within_days' => 7]);

    $connection = SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(20),
    ]);

    expect($connection->needsRefresh())->toBeFalse();
});

test('needs refresh scope returns connections that are expiring soon', function () {
    config(['square.refresh_within_days' => 7]);

    $club = Club::factory()->create();

    $expiringSoon = SquareConnection::factory()->for($club)->create([
        'access_token_expires_at' => now()->addDays(2),
    ]);

    $fresh = SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(30),
    ]);

    $ids = SquareConnection::query()->needsRefresh()->pluck('id')->all();

    expect($ids)->toContain($expiringSoon->id)
        ->and($ids)->not->toContain($fresh->id);
});

test('refresh square access token action updates stored tokens', function () {
    configureSquareOAuthForRefresh();

    $connection = SquareConnection::factory()->create([
        'access_token' => 'old-access-token',
        'refresh_token' => 'old-refresh-token',
        'access_token_expires_at' => now()->addDay(),
    ]);

    $this->mock(SquareOAuthService::class, function ($mock): void {
        $mock->shouldReceive('refreshAccessToken')
            ->once()
            ->andReturn([
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_at' => now()->addDays(30)->toIso8601String(),
            ]);
    });

    $refreshed = app(RefreshSquareAccessTokenAction::class)->handle($connection);

    expect($refreshed->access_token)->toBe('new-access-token')
        ->and($refreshed->refresh_token)->toBe('new-refresh-token')
        ->and($refreshed->access_token_expires_at?->isFuture())->toBeTrue();
});

test('refresh expiring square access tokens action refreshes only eligible connections', function () {
    configureSquareOAuthForRefresh();

    $expiring = SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(2),
    ]);

    SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(30),
    ]);

    $this->mock(SquareOAuthService::class, function ($mock): void {
        $mock->shouldReceive('isConfigured')->andReturn(true);
        $mock->shouldReceive('refreshAccessToken')
            ->once()
            ->andReturn([
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_at' => now()->addDays(30)->toIso8601String(),
            ]);
    });

    $result = app(RefreshExpiringSquareAccessTokensAction::class)->handle();

    expect($result)->toBe(['refreshed' => 1, 'failed' => 0, 'skipped' => 0]);

    $expiring->refresh();

    expect($expiring->access_token)->toBe('new-access-token');
});

test('square refresh command reports refreshed token count', function () {
    configureSquareOAuthForRefresh();

    SquareConnection::factory()->create([
        'access_token_expires_at' => now()->addDays(1),
    ]);

    $this->mock(SquareOAuthService::class, function ($mock): void {
        $mock->shouldReceive('isConfigured')->andReturn(true);
        $mock->shouldReceive('refreshAccessToken')
            ->once()
            ->andReturn([
                'access_token' => 'new-access-token',
                'refresh_token' => 'new-refresh-token',
                'expires_at' => now()->addDays(30)->toIso8601String(),
            ]);
    });

    Artisan::call('square:refresh-access-tokens');

    expect(Artisan::output())->toContain('Refreshed 1 Square access token');
});
