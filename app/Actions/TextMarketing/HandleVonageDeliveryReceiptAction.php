<?php

declare(strict_types=1);

namespace App\Actions\TextMarketing;

use App\Enums\SmsMessageStatus;
use App\Models\SmsMessage;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final readonly class HandleVonageDeliveryReceiptAction
{
    /**
     * @param array<string, mixed> $payload
     */
    public function handle(array $payload): ?SmsMessage
    {
        $messageId = (string) (Arr::get($payload, 'messageId') ?? Arr::get($payload, 'message-id', ''));

        if ($messageId === '') {
            return null;
        }

        $smsMessage = SmsMessage::query()->where('provider_message_id', $messageId)->first();

        if (! $smsMessage) {
            return null;
        }

        $status = Str::of((string) Arr::get($payload, 'status', ''))
            ->lower()
            ->toString();

        if ($status === 'delivered') {
            $smsMessage->markDelivered($this->parseTimestamp($payload));
        } elseif (in_array($status, ['accepted', 'sent', 'submitted', 'buffered'], true)) {
            if ($smsMessage->status === SmsMessageStatus::QUEUED || $smsMessage->status === SmsMessageStatus::SENDING) {
                $smsMessage->markSent($messageId, $this->parseTimestamp($payload));
            }
        } elseif (in_array($status, ['rejected', 'failed', 'expired', 'undeliverable'], true)) {
            $error = (string) (Arr::get($payload, 'error-text') ?? Arr::get($payload, 'error', 'Message delivery failed.'));

            $smsMessage->markFailed($error !== '' ? $error : 'Message delivery failed.');
        }

        $smsMessage->forceFill([
            'metadata' => array_merge($smsMessage->metadata ?? [], [
                'receipt' => $payload,
            ]),
        ])->save();

        return $smsMessage->refresh();
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function parseTimestamp(array $payload): ?CarbonImmutable
    {
        $timestamp = Arr::get($payload, 'timestamp') ?? Arr::get($payload, 'message-timestamp');

        if (! $timestamp) {
            return null;
        }

        try {
            return CarbonImmutable::parse((string) $timestamp);
        } catch (\Throwable) {
            return null;
        }
    }
}
