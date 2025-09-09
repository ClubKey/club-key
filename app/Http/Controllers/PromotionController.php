<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Support\Facades\Gate;
use Inertia\Response;

use function inertia;

final class PromotionController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', Promotion::class);

        $promotions = Promotion::query()->paginate(30);

        return inertia('promotions/Index', [
            'promotions' => $promotions,
        ]);
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
}
