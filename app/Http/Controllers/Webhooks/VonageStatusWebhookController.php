<?php

declare(strict_types=1);

namespace App\Http\Controllers\Webhooks;

use App\Actions\TextMarketing\HandleVonageDeliveryReceiptAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Webhooks\VonageDeliveryStatusRequest;
use Illuminate\Http\JsonResponse;

final class VonageStatusWebhookController extends Controller
{
    public function __invoke(
        VonageDeliveryStatusRequest $request,
        HandleVonageDeliveryReceiptAction $action
    ): JsonResponse {
        $message = $action->handle($request->payload());

        return response()->json([
            'updated' => $message !== null,
        ]);
    }
}
