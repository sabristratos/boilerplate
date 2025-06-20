<?php

namespace App\Providers;

use App\Services\NotificationService;
use Illuminate\Support\ServiceProvider;

class NotificationsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationService::class, fn($app) => new NotificationService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
