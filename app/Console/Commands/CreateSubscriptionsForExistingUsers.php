<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\CommunicationSubscriptions\CreateSubscriptionsForExistingUsersAction;
use Illuminate\Console\Command;
use Throwable;

final class CreateSubscriptionsForExistingUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:create-for-existing-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create communication subscriptions for all existing users who are members of clubs';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(CreateSubscriptionsForExistingUsersAction $action): void
    {
        $this->info('Creating subscriptions for existing users...');

        $bar = $this->output->createProgressBar();
        $bar->start();

        try {
            $count = $action->handle();
            $bar->finish();

            $this->newLine();
            $this->info("Successfully created {$count} subscriptions.");
        } catch (Throwable $e) {
            $bar->finish();
            $this->newLine();
            $this->error('Error creating subscriptions: '.$e->getMessage());
            throw $e;
        }
    }
}
