<?php

declare(strict_types=1);

namespace App\Http\Controllers\CommunicationSubscriptions;

use App\Actions\CommunicationSubscriptions\ResubscribeAction;
use App\Actions\CommunicationSubscriptions\UnsubscribeAction;
use App\Http\Controllers\Controller;
use App\Models\CommunicationSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class UnsubscribeController extends Controller
{
    /**
     * Handle email unsubscribe link.
     *
     * @throws Throwable
     */
    public function email(
        Request $request,
        CommunicationSubscription $subscription,
        UnsubscribeAction $unsubscribeAction
    ): RedirectResponse {
        $unsubscribeAction->handle(
            $subscription,
            reason: 'Email unsubscribe link',
            source: 'email_link'
        );

        return redirect()->route('unsubscribe.confirm', ['subscription' => $subscription]);
    }

    /**
     * Show unsubscribe confirmation page.
     */
    public function confirm(CommunicationSubscription $subscription): Response
    {
        return Inertia::render('Subscribers/Unsubscribe', [
            'subscription' => $subscription->load(['club', 'user']),
        ]);
    }

    /**
     * Handle resubscribe request.
     *
     * @throws Throwable
     */
    public function resubscribe(
        Request $request,
        CommunicationSubscription $subscription,
        ResubscribeAction $resubscribeAction
    ): RedirectResponse {
        $resubscribeAction->handle($subscription, source: 'manual');

        return back()->with('success', 'You have been resubscribed successfully.');
    }
}
