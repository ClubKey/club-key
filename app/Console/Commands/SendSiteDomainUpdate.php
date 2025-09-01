<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\OurWebsiteDomainHasUpdated;
use Illuminate\Console\Command;

use function sleep;

final class SendSiteDomainUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-site-domain-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to all users about site domain update';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $users = User::with('clubs')->get();

        foreach ($users as $user) {
            $user->notify(new OurWebsiteDomainHasUpdated($user));
            $this->info("Notification sent to user ID: {$user->id}, Email: {$user->email}");
            sleep(1);
        }
    }
}
