<?php

declare(strict_types=1);

namespace App\Services\Square;

use App\Models\Club;
use App\Models\SquareConnection;
use Illuminate\Support\Str;
use RuntimeException;
use Square\Exceptions\SquareException;
use Square\OAuth\Requests\ObtainTokenRequest;
use Square\OAuth\Requests\RevokeTokenRequest;
use Square\Types\ObtainTokenResponse;

final class SquareOAuthService
{
    private const string SESSION_CLUB_KEY = 'square_oauth.club_id';

    private const string SESSION_STATE_KEY = 'square_oauth.state';

    public function __construct(private SquareClientFactory $clientFactory) {}

    /**
     * @return array{
     *     access_token: string,
     *     refresh_token: string,
     *     merchant_id: string,
     *     expires_at?: string|null,
     *     scopes?: list<string>|null
     * }
     *
     * @throws SquareException
     */
    public function exchangeAuthorizationCode(string $code): array
    {
        $response = $this->clientFactory->oauth()->obtainToken(
            new ObtainTokenRequest([
                'clientId' => $this->applicationId(),
                'clientSecret' => $this->applicationSecret(),
                'code' => $code,
                'grantType' => 'authorization_code',
                'redirectUri' => $this->redirectUri(),
            ]),
        );

        return $this->tokenResponseFromObtainToken($response, requireMerchantId: true);
    }

    /**
     * @return array{
     *     access_token: string,
     *     refresh_token: string,
     *     merchant_id?: string,
     *     expires_at?: string|null,
     *     scopes?: list<string>|null
     * }
     *
     * @throws SquareException
     */
    public function refreshAccessToken(SquareConnection $connection): array
    {
        $response = $this->clientFactory->oauth()->obtainToken(
            new ObtainTokenRequest([
                'clientId' => $this->applicationId(),
                'clientSecret' => $this->applicationSecret(),
                'grantType' => 'refresh_token',
                'refreshToken' => $connection->refresh_token,
            ]),
        );

        return $this->tokenResponseFromObtainToken($response, requireMerchantId: false);
    }

    public function beginAuthorization(Club $club): string
    {
        $state = Str::random(40);

        session([
            self::SESSION_CLUB_KEY => $club->id,
            self::SESSION_STATE_KEY => $state,
        ]);

        return $this->authorizationUrl($state);
    }

    public function authorizationUrl(string $state): string
    {
        $query = http_build_query([
            'client_id' => $this->applicationId(),
            'scope' => implode(' ', config('square.scopes', [])),
            'state' => $state,
            'redirect_uri' => $this->redirectUri(),
            'session' => 'false',
        ]);

        return $this->authorizeBaseUrl().'/oauth2/authorize?'.$query;
    }

    /**
     * @throws RuntimeException
     */
    public function resolveClubIdFromCallback(?string $state): int
    {
        $expectedState = session(self::SESSION_STATE_KEY);
        $clubId = session(self::SESSION_CLUB_KEY);

        session()->forget([self::SESSION_CLUB_KEY, self::SESSION_STATE_KEY]);

        if ($state === null || $expectedState === null || ! hash_equals($expectedState, $state)) {
            throw new RuntimeException('Invalid Square OAuth state.');
        }

        if (! is_int($clubId) && ! is_numeric($clubId)) {
            throw new RuntimeException('Square OAuth club context is missing.');
        }

        return (int) $clubId;
    }

    /**
     * @throws SquareException
     */
    public function revokeAccessToken(string $accessToken): void
    {
        $this->clientFactory->oauth()->revokeToken(
            new RevokeTokenRequest([
                'clientId' => $this->applicationId(),
                'accessToken' => $accessToken,
            ]),
            [
                'headers' => [
                    'Authorization' => 'Client '.$this->applicationSecret(),
                ],
            ],
        );
    }

    public function isConfigured(): bool
    {
        return filled($this->applicationId())
            && filled($this->applicationSecret())
            && filled($this->redirectUri());
    }

    public function redirectUri(): string
    {
        $uri = config('square.redirect_uri');

        if (! filled($uri)) {
            $uri = route('square.oauth.callback', absolute: true);
        }

        return $this->normalizeRedirectUri((string) $uri);
    }

    /**
     * @return array{
     *     access_token: string,
     *     refresh_token: string,
     *     merchant_id?: string,
     *     expires_at?: string|null,
     *     scopes?: list<string>|null
     * }
     */
    private function tokenResponseFromObtainToken(
        ObtainTokenResponse $response,
        bool $requireMerchantId,
    ): array {
        $accessToken = $response->getAccessToken();
        $refreshToken = $response->getRefreshToken();
        $merchantId = $response->getMerchantId();

        if ($accessToken === null || $refreshToken === null) {
            throw new RuntimeException('Square OAuth token response is missing required fields.');
        }

        if ($requireMerchantId && $merchantId === null) {
            throw new RuntimeException('Square OAuth token response is missing required fields.');
        }

        $tokenResponse = [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'expires_at' => $response->getExpiresAt(),
            'scopes' => config('square.scopes', []),
        ];

        if ($merchantId !== null) {
            $tokenResponse['merchant_id'] = $merchantId;
        }

        return $tokenResponse;
    }

    private function applicationId(): string
    {
        return (string) config('square.application_id');
    }

    private function applicationSecret(): string
    {
        return (string) config('square.application_secret');
    }

    private function normalizeRedirectUri(string $uri): string
    {
        $uri = mb_trim($uri);

        if (preg_match('#^https:(?!//)#', $uri) === 1) {
            $uri = 'https://'.mb_substr($uri, 6);
        }

        if (preg_match('#^http:(?!//)#', $uri) === 1) {
            $uri = 'http://'.mb_substr($uri, 5);
        }

        return mb_rtrim($uri, '/');
    }

    private function authorizeBaseUrl(): string
    {
        return config('square.environment', 'sandbox') === 'sandbox'
            ? 'https://connect.squareupsandbox.com'
            : 'https://connect.squareup.com';
    }
}
