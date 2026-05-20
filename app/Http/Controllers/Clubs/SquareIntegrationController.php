<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Square\DisconnectSquareConnectionAction;
use App\Actions\Square\StoreSquareConnectionAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Services\Square\SquareOAuthService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Square\Exceptions\SquareApiException;
use Square\Exceptions\SquareException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Throwable;

use function to_route;

final class SquareIntegrationController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, Club $club, SquareOAuthService $squareOAuth): Response
    {
        Gate::authorize('update', $club);

        $connection = $club->squareConnection;

        return Inertia::render('clubs/Integrations', [
            'club' => $club,
            'square' => [
                'configured' => $squareOAuth->isConfigured(),
                'redirect_uri' => $squareOAuth->redirectUri(),
                'connected' => $connection !== null,
                'merchant_id' => $connection?->merchant_id,
                'connected_at' => $connection?->connected_at?->toIso8601String(),
                'access_token_expires_at' => $connection?->access_token_expires_at?->toIso8601String(),
                'scopes' => $connection?->scopes ?? [],
            ],
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function connect(Request $request, Club $club, SquareOAuthService $squareOAuth): RedirectResponse|SymfonyResponse
    {
        Gate::authorize('update', $club);

        if (! $squareOAuth->isConfigured()) {
            return to_route('clubs.integrations.square', $club)
                ->with('error', __('Square is not configured. Add your application credentials to the environment.'));
        }

        $authorizationUrl = $squareOAuth->beginAuthorization($club);

        if ($request->inertia()) {
            return Inertia::location($authorizationUrl);
        }

        return redirect()->away($authorizationUrl);
    }

    public function callback(
        Request $request,
        SquareOAuthService $squareOAuth,
        StoreSquareConnectionAction $storeSquareConnection,
    ): RedirectResponse {
        if ($request->filled('error')) {
            return to_route('dashboard')
                ->with('error', __('Square authorization was denied or failed.'));
        }

        $code = $request->string('code')->toString();

        if ($code === '') {
            return to_route('dashboard')
                ->with('error', __('Square did not return an authorization code.'));
        }

        try {
            $clubId = $squareOAuth->resolveClubIdFromCallback($request->string('state')->toString());
            $club = Club::query()->findOrFail($clubId);

            Gate::authorize('update', $club);

            $tokenResponse = $squareOAuth->exchangeAuthorizationCode($code);
            $storeSquareConnection->handle($club, $tokenResponse);

            return to_route('clubs.integrations.square', $club)
                ->with('success', __('Square account connected successfully.'));
        } catch (AuthorizationException) {
            return to_route('dashboard')
                ->with('error', __('You are not authorized to connect Square for this club.'));
        } catch (RuntimeException|SquareException|SquareApiException|Throwable) {
            return to_route('dashboard')
                ->with('error', __('Unable to connect your Square account. Please try again.'));
        }
    }

    /**
     * @throws AuthorizationException|SquareException
     */
    public function disconnect(
        Request $request,
        Club $club,
        DisconnectSquareConnectionAction $disconnectSquareConnection,
    ): RedirectResponse {
        Gate::authorize('update', $club);

        $disconnectSquareConnection->handle($club);

        return to_route('clubs.integrations.square', $club)
            ->with('success', __('Square account disconnected.'));
    }
}
