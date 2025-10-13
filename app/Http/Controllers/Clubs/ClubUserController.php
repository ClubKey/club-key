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
        $clubUsers = $request->user()
            ->currentClub
            ->clubUsers()
            ->search($search)
            ->with('user')
            ->get();

        return Inertia::render('club-users/Index', [
            'club' => $request->user()->currentClub,
            'clubUsers' => $clubUsers,
            'search' => $search,
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
