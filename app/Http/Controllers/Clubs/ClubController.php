<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\CreateClubAction;
use App\Actions\Clubs\DeleteClubAction;
use App\Actions\Clubs\UpdateClubAction;
use App\DTOs\ClubDto;
use App\Enums\ClubRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clubs\ClubRequest;
use App\Http\Requests\Clubs\DeleteClubRequest;
use App\Models\Club;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function to_route;

final class ClubController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        Gate::authorize('create', Club::class);

        return Inertia::render('clubs/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(ClubRequest $request, CreateClubAction $createClub): RedirectResponse
    {
        Gate::authorize('create', Club::class);

        $createClub->handle(ClubDto::fromArray($request->validated()));

        return to_route('dashboard')
            ->with('success', __('Club created successfully.'));
    }

    /**
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    public function show(Request $request, Club $club): Response
    {
        Gate::authorize('view', $club);

        return Inertia::render('clubs/Show', [
            'club' => $club,
            'isMember' => $request->user()?->belongsToClub($club) ?? false,
            'isAdmin' => $request->user()?->hasRoleInClub($club, ClubRole::ADMIN) ?? false,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(Request $request, Club $club): Response
    {
        Gate::authorize('update', $club);

        return Inertia::render('clubs/Edit', [
            'club' => $club,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws AuthorizationException|Throwable
     */
    public function update(ClubRequest $request, Club $club, UpdateClubAction $updateClubAction): RedirectResponse
    {
        Gate::authorize('update', $club);

        $updateClubAction->handle(ClubDto::fromArray($request->validated()), $club);

        return to_route('clubs.edit', $club)->with('success', __('Club updated successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function dangerZone(Request $request, Club $club): Response
    {
        Gate::authorize('delete', $club);

        return Inertia::render('clubs/DangerZone', [
            'club' => $club,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Throwable
     */
    public function destroy(DeleteClubRequest $request, Club $club, DeleteClubAction $deleteClubAction): RedirectResponse
    {
        Gate::authorize('delete', $club);

        $deleteClubAction->handle($club, $request->user());

        return to_route('dashboard')
            ->with('success', __('Club deleted successfully.'));
    }
}
