<?php

declare(strict_types=1);

namespace App\Http\Controllers\ClubRewards;

use App\Actions\ClubRewards\CreateRewardAction;
use App\Actions\ClubRewards\DeleteRewardAction;
use App\Actions\ClubRewards\UpdateRewardAction;
use App\DTOs\RewardDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClubRewards\RewardRequest;
use App\Models\Reward;
use App\Models\RewardLevel;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

use function to_route;

final class RewardController extends Controller
{
    public function __construct(
        private readonly CreateRewardAction $createRewardAction,
        private readonly UpdateRewardAction $updateRewardAction,
        private readonly DeleteRewardAction $deleteRewardAction,
    ) {}

    /**
     * Show the form for creating a new resource.
     *
     * @throws AuthorizationException
     */
    public function create(RewardLevel $rewardLevel): Response
    {
        Gate::authorize('create', Reward::class);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        return Inertia::render('club-rewards/Rewards/Create', [
            'rewardLevel' => $rewardLevel,
            'club' => $club,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Throwable
     */
    public function store(RewardRequest $request, RewardLevel $rewardLevel): RedirectResponse
    {
        Gate::authorize('create', Reward::class);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        $dto = RewardDto::fromArray(array_merge($request->validated(), ['reward_level_id' => $rewardLevel->id]));

        $this->createRewardAction->handle($dto);

        return to_route('reward-levels.edit', $rewardLevel)
            ->with('message', 'Reward created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws AuthorizationException
     */
    public function edit(RewardLevel $rewardLevel, Reward $reward): Response
    {
        Gate::authorize('update', $reward);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        // Ensure the reward belongs to the reward level
        if ($reward->reward_level_id !== $rewardLevel->id) {
            abort(404, 'Reward not found.');
        }

        $reward->load('rewardLevel.club');

        return Inertia::render('club-rewards/Rewards/Edit', [
            'reward' => $reward,
            'club' => $club,
            'rewardLevel' => $rewardLevel,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws Throwable
     */
    public function update(RewardRequest $request, RewardLevel $rewardLevel, Reward $reward): RedirectResponse
    {
        Gate::authorize('update', $reward);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        // Ensure the reward belongs to the reward level
        if ($reward->reward_level_id !== $rewardLevel->id) {
            abort(404, 'Reward not found.');
        }

        $dto = RewardDto::fromArray(array_merge($request->validated(), ['reward_level_id' => $rewardLevel->id]));

        $this->updateRewardAction->handle($dto, $reward);

        return to_route('reward-levels.edit', $reward->rewardLevel)
            ->with('message', 'Reward updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws Throwable
     */
    public function destroy(RewardLevel $rewardLevel, Reward $reward): RedirectResponse
    {
        Gate::authorize('delete', $reward);

        $club = Auth::user()->currentClub;

        if (! $club) {
            abort(404, 'No current club selected.');
        }

        // Ensure the reward level belongs to the current club
        if ($rewardLevel->club_id !== $club->id) {
            abort(404, 'Reward level not found.');
        }

        // Ensure the reward belongs to the reward level
        if ($reward->reward_level_id !== $rewardLevel->id) {
            abort(404, 'Reward not found.');
        }

        $rewardLevel = $reward->rewardLevel;
        $this->deleteRewardAction->handle($reward);

        return to_route('reward-levels.edit', $rewardLevel)
            ->with('message', 'Reward deleted successfully!');
    }
}
