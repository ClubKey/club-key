<?php

declare(strict_types=1);

namespace App\Http\Controllers\Promotions;

use App\Actions\Promotions\SendPromotionNotificationAction;
use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Throwable;

final class ResendPromotionNotification extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Throwable
     */
    public function __invoke(Request $request, Promotion $promotion, SendPromotionNotificationAction $action)
    {
        Gate::authorize('update', $promotion);

        if (! $promotion->published_at) {
            return back()->with([
                'error' => 'Promotion is not published yet. Please publish promotion. When your promotion is published, a notification will automatically be sent.',
            ]);
        }

        $action->handle($promotion);

        return back()->with('success', 'Promotion notification has been sent to the queue for sending!');
    }
}
