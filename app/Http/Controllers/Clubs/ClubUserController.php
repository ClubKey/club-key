<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use App\Models\ClubUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class ClubUserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', [ClubUser::class, $request->user()->currentClub]);
        $search = $request->input('search');
        $sort = $request->input('sort');

        $query = $request->user()
            ->currentClub
            ->clubUsers()
            ->search($search);

        // Apply sorting
        match ($sort) {
            'most_checkins' => $query->orderBy('club_user.visits_past_30_days', 'desc'),
            'recent_visit' => $query
                ->addSelect([
                    'last_visit_at' => function ($query) use ($request) {
                        $query->selectRaw('MAX(created_at)')
                            ->from('visits')
                            ->whereColumn('visits.user_id', 'club_user.user_id')
                            ->where('visits.club_id', $request->user()->currentClub->id);
                    },
                ])
                ->orderByDesc('last_visit_at'),
            'reward_level' => $query
                ->addSelect([
                    'reward_level_order' => function ($query) {
                        $query->select('order')
                            ->from('reward_levels')
                            ->whereColumn('reward_levels.id', 'club_user.reward_level_id')
                            ->limit(1);
                    },
                ])
                ->orderByDesc('reward_level_order'),
            default => $query->orderBy('club_user.id', 'asc'),
        };

        $clubUsers = Inertia::scroll(fn () => $query->with('user')->paginate(40));

        return Inertia::render('club-users/Index', [
            'club' => $request->user()->currentClub,
            'clubUsers' => $clubUsers,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, ClubUser $member): Response
    {
        Gate::authorize('view', $member);

        return inertia('club-users/Show', [
            'clubUser' => $member,
            'user' => $member->user,
            'club' => $member->club,
        ]);
    }
}
