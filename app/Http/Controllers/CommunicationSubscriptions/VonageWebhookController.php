<?php

declare(strict_types=1);

namespace App\Http\Controllers\CommunicationSubscriptions;

use App\Actions\CommunicationSubscriptions\UnsubscribeAction;
use App\Enums\ContactType;
use App\Enums\SubscriptionStatus;
use App\Http\Controllers\Controller;
use App\Models\CommunicationSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

final class VonageWebhookController extends Controller
{
    /**
     * Handle Vonage SMS webhook.
     *
     * @throws Throwable
     */
    public function handle(
        Request $request,
        UnsubscribeAction $unsubscribeAction
    ): JsonResponse {
        $data = $request->all();

        // Log webhook for debugging
        Log::info('Vonage webhook received', $data);

        // Check for STOP keywords in the message text
        $messageText = mb_strtoupper(mb_trim($data['text'] ?? $data['message'] ?? ''));
        $stopKeywords = ['STOP', 'STOPALL', 'UNSUBSCRIBE', 'QUIT', 'END', 'CANCEL'];

        $isStopMessage = false;
        foreach ($stopKeywords as $keyword) {
            if (str_contains($messageText, $keyword)) {
                $isStopMessage = true;
                break;
            }
        }

        if (! $isStopMessage) {
            return response()->json(['status' => 'ok']);
        }

        // Extract phone number from webhook
        $phoneNumber = $data['msisdn'] ?? $data['from'] ?? null;

        if (! $phoneNumber) {
            Log::warning('Vonage webhook missing phone number', $data);

            return response()->json(['status' => 'error', 'message' => 'Missing phone number'], 400);
        }

        // Normalize phone number
        $normalizedPhone = format_phone_number($phoneNumber);

        // Find all SMS subscriptions for this phone number and unsubscribe them
        $subscriptions = CommunicationSubscription::where('contact_type', ContactType::SMS)
            ->where('contact_value', $normalizedPhone)
            ->where('status', SubscriptionStatus::SUBSCRIBED)
            ->get();

        foreach ($subscriptions as $subscription) {
            try {
                $unsubscribeAction->handle(
                    $subscription,
                    reason: "STOP keyword via SMS: {$messageText}",
                    source: 'vonage_webhook',
                    providerMessageId: $data['messageId'] ?? $data['message-id'] ?? null,
                    providerPayload: $data
                );
            } catch (Throwable $e) {
                Log::error('Failed to unsubscribe via webhook', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                    'webhook_data' => $data,
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }
}
