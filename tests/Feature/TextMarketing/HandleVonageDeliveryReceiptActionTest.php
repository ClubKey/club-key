<?php

declare(strict_types=1);

use App\Actions\TextMarketing\HandleVonageDeliveryReceiptAction;
use App\Enums\SmsMessageStatus;
use App\Models\SmsMessage;
use Carbon\CarbonImmutable;

it('marks messages as delivered when vonage sends delivered receipts', function (): void {
    $message = SmsMessage::factory()->create([
        'status' => SmsMessageStatus::SENT,
        'provider_message_id' => 'abc123',
    ]);

    /** @var HandleVonageDeliveryReceiptAction $action */
    $action = app(HandleVonageDeliveryReceiptAction::class);

    $action->handle([
        'messageId' => 'abc123',
        'status' => 'delivered',
        'message-timestamp' => CarbonImmutable::now()->toIso8601String(),
    ]);

    expect($message->fresh()->status)->toBe(SmsMessageStatus::DELIVERED);
});

it('marks messages as failed when vonage reports a failure', function (): void {
    $message = SmsMessage::factory()->create([
        'status' => SmsMessageStatus::SENDING,
        'provider_message_id' => 'failure-1',
    ]);

    /** @var HandleVonageDeliveryReceiptAction $action */
    $action = app(HandleVonageDeliveryReceiptAction::class);

    $action->handle([
        'messageId' => 'failure-1',
        'status' => 'failed',
        'error-text' => 'Rejected by carrier',
    ]);

    $updated = $message->fresh();

    expect($updated->status)->toBe(SmsMessageStatus::FAILED)
        ->and($updated->failure_reason)->toBe('Rejected by carrier');
});
