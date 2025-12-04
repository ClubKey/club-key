<?php

declare(strict_types=1);

namespace App\Http\Controllers\Promotions;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

final class PublicPromotionLinkController extends Controller
{
    /**
     * Handle the incoming request. Redirect the user to the club page if the promotion doesn't have a link
     */
    public function __invoke(Request $request, Promotion $promotion)
    {
        if ($promotion->link) {
            return redirect($promotion->link);
        }

        return redirect(route('clubs.show', $promotion->club->slug));
    }
}
