<?php

declare(strict_types=1);

namespace App\Services\TextMarketing;

use App\Enums\SmsDirection;
use App\Enums\SmsMessageStatus;
use App\Jobs\SendSmsMessageJob;
use App\Models\Club;
use App\Models\ClubSmsSetting;
use App\Models\SmsMessage;
use App\Models\TextSubscription;
use App\Support\TextMarketingPlan;
use Carbon\CarbonImmutable;
use DomainException;
use Illuminate\Support\Str;

final readonly class TextMarketingService
{
    public function queueMessage(
        Club $club,
        TextSubscription $subscription,
        string $body,
        array $metadata = []
    ): SmsMessage {
        if (! $subscription->is_subscribed) {
            throw new DomainException('Cannot send SMS to an unsubscribed member.');
        }

        $settings = $club->smsSetting;

        if (! $settings) {
            throw new DomainException('Club is not configured for text message marketing.');
        }

        $messageBody = $this->decorateMessageBody($body);
        $segments = $this->calculateSegments($messageBody);

        $smsMessage = SmsMessage::query()->create([
            'club_id' => $club->id,
            'text_subscription_id' => $subscription->id,
            'direction' => SmsDirection::OUTBOUND,
            'status' => SmsMessageStatus::QUEUED,
            'from_number' => $settings->vonage_number,
            'to_number' => $subscription->phone_number,
            'segments' => $segments,
            'body' => $messageBody,
            'metadata' => $metadata,
            'queued_at' => CarbonImmutable::now(),
        ]);

        SendSmsMessageJob::dispatch($smsMessage->id);

        return $smsMessage;
    }

    public function decorateMessageBody(string $body): string
    {
        $footer = trim((string) config('text-marketing.opt_out_footer'));

        if ($footer === '') {
            return trim($body);
        }

        $normalizedBody = Str::of($body)->trim();
        $footerAlreadyPresent = Str::of($normalizedBody)->lower()->contains(Str::of($footer)->lower());

        if ($footerAlreadyPresent) {
            return (string) $normalizedBody;
        }

        return sprintf('%s %s', $normalizedBody, $footer);
    }

    public function calculateSegments(string $body): int
    {
        $length = mb_strlen($body, 'UTF-8');

        if ($length === 0) {
            return 1;
        }

        $segmentLength = 160;

        if ($this->containsNonGsmCharacters($body)) {
            $segmentLength = 70;
        }

        return (int) max(1, ceil($length / $segmentLength));
    }

    public function configurePlan(Club $club, string $planIdentifier): ClubSmsSetting
    {
        $plan = TextMarketingPlan::fromConfig($planIdentifier);

        $settings = $club->smsSetting ?? new ClubSmsSetting([
            'club_id' => $club->id,
        ]);

        $settings->forceFill([
            'current_plan' => $plan->identifier,
            'monthly_limit' => $plan->monthlyLimit,
            'base_price_cents' => $plan->basePriceCents,
            'overage_rate_cents' => $plan->overageRateCents,
            'billing_cycle_resets_at' => CarbonImmutable::now()->addMonth(),
        ])->save();

        return $settings;
    }

    public function recordInboundMessage(
        ClubSmsSetting $settings,
        string $fromNumber,
        string $toNumber,
        string $body,
        array $metadata = []
    ): SmsMessage {
        $segments = $this->calculateSegments($body);

        $message = SmsMessage::query()->create([
            'club_id' => $settings->club_id,
            'direction' => SmsDirection::INBOUND,
            'status' => SmsMessageStatus::RECEIVED,
            'from_number' => $fromNumber,
            'to_number' => $toNumber,
            'segments' => $segments,
            'body' => trim($body),
            'metadata' => $metadata,
        ]);

        $settings->incrementReceivedMessages($segments);

        return $message;
    }

    private function containsNonGsmCharacters(string $body): bool
    {
        return strlen($body) !== mb_strlen($body, 'UTF-8');
    }
}
