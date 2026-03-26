<?php

declare(strict_types=1);

namespace App\Http\Controllers\CommunicationSubscriptions;

use App\Http\Controllers\Controller;
use App\Models\CommunicationSubscription;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class SubscriberController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        $club = $request->user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        Gate::authorize('viewAny', [CommunicationSubscription::class, $club]);

        $query = $club->communicationSubscriptions()
            ->with(['user'])
            ->orderByDesc('created_at');

        // Filter by contact type
        if ($request->has('contact_type') && $request->string('contact_type')->isNotEmpty()) {
            $query->where('contact_type', $request->string('contact_type'));
        }

        // Filter by topic
        if ($request->has('topic') && $request->string('topic')->isNotEmpty()) {
            $query->where('topic', $request->string('topic'));
        }

        // Filter by status
        if ($request->has('status') && $request->string('status')->isNotEmpty()) {
            $query->where('status', $request->string('status'));
        }

        // Search by contact value
        if ($request->has('search') && $request->string('search')->isNotEmpty()) {
            $search = $request->string('search');
            $query->where('contact_value', 'like', "%{$search}%");
        }

        $subscriptions = $query->paginate(50)->withQueryString();

        return Inertia::render('Subscribers/Index', [
            'club' => $club,
            'subscriptions' => $subscriptions,
            'filters' => $request->only(['contact_type', 'topic', 'status', 'search']),
        ]);
    }
}
