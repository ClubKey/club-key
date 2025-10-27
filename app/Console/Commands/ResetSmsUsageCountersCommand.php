<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ClubSmsSetting;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

final class ResetSmsUsageCountersCommand extends Command
{
    protected $signature = 'text-marketing:reset-usage';

    protected $description = 'Reset monthly SMS usage counters for all clubs.';

    public function handle(): int
    {
        $now = CarbonImmutable::now();

        ClubSmsSetting::query()
            ->lazyById()
            ->each(function (ClubSmsSetting $settings) use ($now): void {
                $settings->forceFill([
                    'messages_sent_this_period' => 0,
                    'messages_received_this_period' => 0,
                    'overage_messages' => 0,
                    'billing_cycle_resets_at' => $now->addMonth(),
                ])->save();
            });

        $this->info('SMS usage counters have been reset.');

        return self::SUCCESS;
    }
}
