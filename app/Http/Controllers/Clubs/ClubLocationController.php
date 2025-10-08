<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\CreateClubLocationAction;
use App\Actions\Clubs\DeleteClubLocationAction;
use App\Actions\Clubs\UpdateClubLocationAction;
use App\DTOs\ClubLocationDto;
use App\Enums\ClubRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Clubs\ClubLocationRequest;
use App\Models\ClubLocation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function to_route;

final class ClubLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', ClubLocation::class);

        $club = Auth::user()->currentClub;

        if (!$club) {
            abort(404, 'No current club selected.');
        }

        $locations = $club->locations()->orderBy('name')->get();
        if (auth()->user()->hasRoleInClub($club, ClubRole::ADMIN)) {
            // append the visit_tap_link to the locations
            $locations->append('visit_tap_link');
        }

        return Inertia::render('locations/Index', [
            'club' => $club,
            'locations' => $locations,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        Gate::authorize('create', ClubLocation::class);

        $club = Auth::user()->currentClub;

        if (!$club) {
            abort(404, 'No current club selected.');
        }

        return Inertia::render('locations/Create', [
            'club' => $club,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(ClubLocationRequest $request, CreateClubLocationAction $createLocation): RedirectResponse
    {
        Gate::authorize('create', ClubLocation::class);

        $club = Auth::user()->currentClub;

        if (!$club) {
            abort(404, 'No current club selected.');
        }

        $data = $request->validated();
        $data['club_id'] = $club->id;

        $createLocation->handle(ClubLocationDto::fromArray($data));

        return to_route('locations.index')
            ->with('success', __('Location created successfully.'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(ClubLocation $location): Response
    {
        Gate::authorize('update', $location);

        $club = Auth::user()->currentClub;

        if (!$club) {
            abort(404, 'No current club selected.');
        }

        return Inertia::render('locations/Edit', [
            'club' => $club,
            'location' => $location,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws AuthorizationException|Throwable
     */
    public function update(ClubLocationRequest $request, ClubLocation $location, UpdateClubLocationAction $updateLocation): RedirectResponse
    {
        Gate::authorize('update', $location);

        $club = Auth::user()->currentClub;

        if (!$club) {
            abort(404, 'No current club selected.');
        }

        $data = $request->validated();
        $data['club_id'] = $club->id;

        $updateLocation->handle(ClubLocationDto::fromArray($data), $location);

        return to_route('locations.index')
            ->with('success', __('Location updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Throwable
     */
    public function destroy(ClubLocation $location, DeleteClubLocationAction $deleteLocation): RedirectResponse
    {
        Gate::authorize('delete', $location);

        $deleteLocation->handle($location);

        return to_route('locations.index')
            ->with('success', __('Location deleted successfully.'));
    }
}
