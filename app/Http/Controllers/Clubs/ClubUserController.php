<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clubs;

use App\Enums\ClubRole;
use App\Http\Controllers\Controller;
use App\Models\ClubUser;
use App\Models\RewardLevel;
use App\Models\User;
use App\Support\DataTable\DataTable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
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

        $club = $request->user()->currentClub;

        $dataTable = DataTable::for(
            ClubUser::query()
                ->where('club_id', $club->id)
                ->with(['user', 'club', 'rewardLevel', 'nextRewardLevel'])
                ->withCount([
                    'visits as total_visits',
                    'visits as visits_last_week' => fn (Builder $builder) => $builder->where('created_at', '>=', now()->subDays(7)),
                ])
        )
            ->searchable([
                static function (Builder $builder, string $search): void {
                    $builder->orWhereHas('user', function (Builder $userQuery) use ($search): void {
                        $userQuery->where(function (Builder $query) use ($search): void {
                            $query->where('name', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        });
                    });
                },
            ])
            ->filter('role', static function (Builder $builder, array|string $roles): void {
                $roleValues = array_filter((array) $roles, static fn ($role) => $role !== null && $role !== '');

                if ($roleValues === []) {
                    return;
                }

                $builder->whereIn('role', $roleValues);
            })
            ->filter('reward_level_id', static function (Builder $builder, array|string $rewardLevels): void {
                $levelIds = array_filter((array) $rewardLevels, static fn ($id) => $id !== null && $id !== '');

                if ($levelIds === []) {
                    return;
                }

                $builder->whereIn('reward_level_id', $levelIds);
            })
            ->sortable('most_visits', 'total_visits', 'desc')
            ->sortable('most_visits_last_week', static function (Builder $builder, string $direction): void {
                $builder->orderBy('visits_last_week', $direction);
            }, 'desc')
            ->sortable('name', static function (Builder $builder, string $direction): void {
                $builder->orderBy(
                    User::select('name')
                        ->whereColumn('users.id', 'club_user.user_id')
                        ->limit(1),
                    $direction
                );
            })
            ->sortable('new_members', 'created_at', 'desc')
            ->sortable('old', 'created_at', 'asc')
            ->defaultSort('new_members', 'desc')
            ->apply($request);

        $clubUsers = $dataTable->builder()->get();

        return inertia('club-users/Index', [
            'club' => $club,
            'clubUsers' => $clubUsers,
            'dataTable' => [
                'state' => $dataTable->state(),
                'sorts' => [
                    ['label' => 'Most visits', 'value' => 'most_visits', 'direction' => 'desc'],
                    ['label' => 'Most visits last week', 'value' => 'most_visits_last_week', 'direction' => 'desc'],
                    ['label' => 'Name', 'value' => 'name', 'direction' => 'asc'],
                    ['label' => 'New members', 'value' => 'new_members', 'direction' => 'desc'],
                    ['label' => 'Oldest members', 'value' => 'old', 'direction' => 'asc'],
                ],
                'filters' => [
                    'role' => [
                        'label' => 'Role',
                        'type' => 'multi',
                        'options' => array_map(
                            static fn (ClubRole $role) => [
                                'label' => $role->label(),
                                'value' => $role->value,
                            ],
                            ClubRole::cases()
                        ),
                    ],
                    'reward_level_id' => [
                        'label' => 'Reward level',
                        'type' => 'multi',
                        'options' => $club->rewardLevels()
                            ->get(['id', 'name'])
                            ->map(static fn (RewardLevel $level) => [
                                'label' => $level->name,
                                'value' => (string) $level->id,
                            ])->all(),
                    ],
                ],
            ],
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
