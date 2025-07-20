<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Actions\Clubs\CreateClubAction;
use App\DTOs\ClubDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRequest;
use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

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

        $club = $createClub->handle(ClubDto::fromArray($request->validated()));

        return redirect()->route('dashboard');
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
