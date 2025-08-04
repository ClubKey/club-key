<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

final class ClubUserController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', [ClubUser::class, $request->user()->currentClub]);

        $clubUsers = $request->user()->currentClub->clubUsers()->with('user')->get();

        return inertia('admin/club-users/Index', [
            'club' => $request->user()->currentClub,
            'clubUsers' => $clubUsers,
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Request $request, ClubUser $member): Response
    {
        Gate::authorize('view', $member);

        return inertia('admin/club-users/Show', [
            'clubUser' => $member,
            'user' => $member->user,
            'club' => $member->club,
        ]);
    }
}
