<?php

declare(strict_types=1);

namespace App\Http\Controllers\Subscriptions;

use App\Actions\Subscriptions\CreateSubscriptionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\CreateSubscriptionRequest;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function to_route;

final class SubscriptionController extends Controller
{
    /**
     * Show the subscription creation form.
     */
    public function create(Club $club): Response
    {
        Gate::authorize('update', $club);

        return Inertia::render('subscriptions/Create', [
            'club' => $club,
            'adminCount' => $club->getAdminCount(),
            'subscriptionPrice' => $club->calculateSubscriptionPrice(),
        ]);
    }

    /**
     * Store a newly created subscription.
     *
     * @throws Throwable
     */
    public function store(CreateSubscriptionRequest $request, Club $club, CreateSubscriptionAction $createSubscriptionAction): RedirectResponse
    {
        Gate::authorize('update', $club);

        try {
            $createSubscriptionAction->handle(
                $club,
                $request->input('payment_method'),
                $request->input('price_id', 'price_club_base')
            );

            return to_route('clubs.show', $club)
                ->with('success', __('Subscription created successfully.'));
        } catch (Throwable $e) {
            return back()
                ->with('error', __('Failed to create subscription: :message', ['message' => $e->getMessage()]));
        }
    }

    /**
     * Show the billing portal.
     */
    public function portal(Club $club): RedirectResponse
    {
        Gate::authorize('update', $club);

        return $club->redirectToBillingPortal(route('clubs.show', $club));
    }
}
