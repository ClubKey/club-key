<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\TextMarketing\HandleVonageInboundMessageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Webhooks\VonageInboundRequest;
use Illuminate\Http\JsonResponse;

final class VonageInboundWebhookController extends Controller
{
    public function __invoke(
        VonageInboundRequest $request,
        HandleVonageInboundMessageAction $action
    ): JsonResponse {
        $message = $action->handle($request->payload());

        return response()->json([
            'received' => $message !== null,
        ]);
    }
}
