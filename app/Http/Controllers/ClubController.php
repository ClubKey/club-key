<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateClubAction;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

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
    public function create()
    {
        Gate::authorize('create', Club::class);

        return Inertia::render('clubs/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ClubRequest $request, CreateClubAction $createClub)
    {
        Gate::authorize('create', Club::class);

        $club = $createClub->handle($request);

        return redirect()->route('clubs.show', $club);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
