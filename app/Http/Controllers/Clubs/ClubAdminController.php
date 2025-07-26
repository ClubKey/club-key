<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class ClubAdminController extends Controller
{
    /**
     * @throws AuthorizationException
     */
    public function index(Request $request, Club $club): Response
    {
        Gate::authorize('update', $club);

        $admins = $club->admins;
        $adminInvitations = $club->adminInvitations;

        return Inertia::render('clubs/ClubAdmins', [
            'club' => $club,
            'admins' => $admins,
            'adminInvitations' => $adminInvitations,
        ]);
    }
}
