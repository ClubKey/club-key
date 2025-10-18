<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Promotions\CreatePromotionAction;
use App\Actions\Promotions\DeletePromotionAction;
use App\Actions\Promotions\UpdatePromotionAction;
use App\DTOs\PromotionDto;
use App\Http\Requests\Promotions\PromotionRequest;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

use Throwable;
use function inertia;
use function to_route;

final class PromotionController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Promotion::class);
        $club = Auth::user()->currentClub;

        $promotions = $club->promotions()->paginate(30);

        return inertia('promotions/Index', [
            'promotions' => $promotions,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(PromotionRequest $request, CreatePromotionAction $action): RedirectResponse
    {
        Gate::authorize('create', Promotion::class);

        $validated = $request->validated();

        $action->handle(PromotionDto::fromArray($validated), Auth::user()->currentClub);

        return to_route('promotions.index')->with(
            'success',
            'Promotion created successfully.'
        );
    }

    public function show(Promotion $promotion): Response
    {
        Gate::authorize('view', $promotion);

        return inertia('promotions/Show', [
            'promotion' => $promotion,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Promotion::class);

        return inertia('promotions/Create');
    }

    public function edit(Promotion $promotion): Response
    {
        Gate::authorize('update', $promotion);

        return inertia('promotions/Edit', [
            'promotion' => $promotion,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function update(
        PromotionRequest $request,
        Promotion $promotion,
        UpdatePromotionAction $action
    ): RedirectResponse {
        Gate::authorize('update', $promotion);

        $validated = $request->validated();

        $action->handle($promotion, PromotionDto::fromArray($validated));

        return to_route('promotions.show', $promotion)->with(
            'success',
            'Promotion updated successfully.'
        );
    }

    /**
     * @throws Throwable
     */
    public function destroy(Promotion $promotion, DeletePromotionAction $action): RedirectResponse
    {
        Gate::authorize('delete', $promotion);

        $action->handle($promotion);

        return to_route('promotions.index')->with(
            'success',
            'Promotion deleted successfully.'
        );
    }
}
