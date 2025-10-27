<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SmsMessageStatus;
use App\Models\SmsMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Vonage\Client as VonageClient;
use Vonage\SMS\Message\SMS as VonageSmsMessage;

final class SendSmsMessageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly int $smsMessageId
    ) {}

    public function handle(VonageClient $vonageClient): void
    {
        $message = SmsMessage::query()
            ->with(['club.smsSetting', 'subscription'])
            ->find($this->smsMessageId);

        if (! $message) {
            return;
        }

        if ($message->status !== SmsMessageStatus::QUEUED) {
            return;
        }

        $settings = $message->club->smsSetting;

        if (! $settings) {
            $message->markFailed('Club does not have SMS settings configured.');

            return;
        }

        if ($message->subscription && ! $message->subscription->is_subscribed) {
            $message->markFailed('Recipient is unsubscribed.');

            return;
        }

        $fromNumber = $message->from_number ?? $settings->vonage_number;
        $toNumber = $message->to_number ?? $message->subscription?->phone_number;

        if (! $toNumber) {
            $message->markFailed('Recipient phone number is missing.');

            return;
        }

        $message->forceFill([
            'status' => SmsMessageStatus::SENDING,
            'from_number' => $fromNumber,
            'to_number' => $toNumber,
        ])->save();

        try {
            $vonageResponse = $vonageClient->sms()->send(new VonageSmsMessage(
                $toNumber,
                $fromNumber,
                $message->body,
            ));

            $currentMessage = $vonageResponse->current();
            $messageId = is_object($currentMessage) && method_exists($currentMessage, 'getMessageId')
                ? (string) $currentMessage->getMessageId()
                : (string) ($currentMessage['message-id'] ?? '');

            $message->markSent($messageId !== '' ? $messageId : (string) $vonageResponse->count());

            $settings->incrementSentMessages($message->segments);
        } catch (Throwable $throwable) {
            $message->markFailed($throwable->getMessage());

            report($throwable);
        }
    }
}
