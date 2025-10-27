<?php

declare(strict_types=1);

use App\Console\Commands\ResetSmsUsageCountersCommand;
use App\Console\Commands\UpdateClubUsersRewards;
use App\Jobs\PublishDuePromotionsJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(UpdateClubUsersRewards::class)->dailyAt('7:00');
Schedule::job(PublishDuePromotionsJob::class)->everyThirtyMinutes();
Schedule::command(ResetSmsUsageCountersCommand::class)->monthlyOn(1, '01:00');
