<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClubRewards;

use App\Actions\ClubRewards\CreateRewardLevelAction;
use App\Actions\ClubRewards\DeleteRewardLevelAction;
use App\Actions\ClubRewards\UpdateRewardLevelAction;
use App\DTOs\RewardLevelDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRewards\RewardLevelRequest;
use App\Models\RewardLevel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function to_route;

final class RewardLevelController extends Controller
{
    public function __construct(
        private readonly CreateRewardLevelAction $createRewardLevelAction,
        private readonly UpdateRewardLevelAction $updateRewardLevelAction,
        private readonly DeleteRewardLevelAction $deleteRewardLevelAction,
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @throws AuthorizationException
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', RewardLevel::class);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        $rewardLevels = $club->rewardLevels()->with('rewards')->orderBy('order')->get();

        return Inertia::render('club-rewards/RewardLevels/Index', [
            'club' => $club,
            'rewardLevels' => $rewardLevels,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     */
    public function create(): Response
    {
        Gate::authorize('create', RewardLevel::class);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        return Inertia::render('club-rewards/RewardLevels/Create', [
            'club' => $club,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(RewardLevelRequest $request): RedirectResponse
    {
        Gate::authorize('create', RewardLevel::class);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        $dto = RewardLevelDto::fromArray(array_merge($request->validated(), ['club_id' => $club->id]));

        $this->createRewardLevelAction->handle($dto);

        return to_route('reward-levels.index')
            ->with('message', 'Reward level created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(RewardLevel $rewardLevel): Response
    {
        Gate::authorize('update', $rewardLevel);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        $rewardLevel->load('rewards');

        return Inertia::render('club-rewards/RewardLevels/Edit', [
            'club' => $club,
            'rewardLevel' => $rewardLevel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws Throwable
     */
    public function update(RewardLevelRequest $request, RewardLevel $rewardLevel): RedirectResponse
    {
        Gate::authorize('update', $rewardLevel);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        $dto = RewardLevelDto::fromArray(array_merge($request->validated(), ['club_id' => $club->id]));

        $this->updateRewardLevelAction->handle($dto, $rewardLevel);

        return to_route('reward-levels.index')
            ->with('message', 'Reward level updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Throwable
     */
    public function destroy(RewardLevel $rewardLevel): RedirectResponse
    {
        Gate::authorize('delete', $rewardLevel);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        $this->deleteRewardLevelAction->handle($rewardLevel);

        return to_route('reward-levels.index')
            ->with('message', 'Reward level deleted successfully!');
    }
}
