<?php

declare(strict_types=1);

namespace App\Providers;

use App\Events\AdminAddedToClub;
use App\Events\AdminRemovedFromClub;
use App\Listeners\UpdateClubSubscriptionListener;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading();

        // Register event listeners
        Event::listen(
            [AdminAddedToClub::class, AdminRemovedFromClub::class],
            [UpdateClubSubscriptionListener::class, 'handle']
        );
    }
}
