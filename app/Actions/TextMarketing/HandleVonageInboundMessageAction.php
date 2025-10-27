<?php

declare(strict_types=1);

namespace App\Actions\TextMarketing;

use App\Models\ClubSmsSetting;
use App\Models\SmsMessage;
use App\Models\TextSubscription;
use App\Services\TextMarketing\TextMarketingService;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

final readonly class HandleVonageInboundMessageAction
{
    public function __construct(private TextMarketingService $service) {}

    /**
     * @param array<string, mixed> $payload
     */
    public function handle(array $payload): ?SmsMessage
    {
        $toNumber = (string) Arr::get($payload, 'to');
        $fromNumber = (string) Arr::get($payload, 'msisdn');
        $text = (string) Arr::get($payload, 'text', '');

        if ($toNumber === '' || $fromNumber === '') {
            return null;
        }

        $settings = ClubSmsSetting::query()->where('vonage_number', $toNumber)->first();

        if (! $settings) {
            return null;
        }

        $subscription = TextSubscription::query()->firstOrCreate(
            [
                'club_id' => $settings->club_id,
                'phone_number' => $fromNumber,
            ],
            [
                'is_subscribed' => true,
                'opt_in_source' => 'inbound_sms',
            ]
        );

        $message = $this->service->recordInboundMessage(
            $settings,
            $fromNumber,
            $toNumber,
            $text,
            $payload
        );

        $message->forceFill([
            'text_subscription_id' => $subscription->id,
        ])->save();

        if ($this->containsOptOutKeyword($text)) {
            $subscription->markUnsubscribed();
        }

        if ($this->containsOptInKeyword($text)) {
            $subscription->markSubscribed('inbound_keyword');
        }

        return $message->refresh();
    }

    private function containsOptOutKeyword(string $text): bool
    {
        $normalized = Str::of($text)->lower();

        return collect(config('text-marketing.opt_out_keywords', []))
            ->filter()
            ->map(static fn ($keyword) => Str::lower((string) $keyword))
            ->contains(static fn (string $keyword) => $normalized->contains($keyword));
    }

    private function containsOptInKeyword(string $text): bool
    {
        $normalized = Str::of($text)->lower();

        return collect(config('text-marketing.opt_in_keywords', []))
            ->filter()
            ->map(static fn ($keyword) => Str::lower((string) $keyword))
            ->contains(static fn (string $keyword) => $normalized->contains($keyword));
    }
}
