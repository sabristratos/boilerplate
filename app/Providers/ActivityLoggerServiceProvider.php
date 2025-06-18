<?php

namespace App\Providers;

use App\Services\ActivityLoggerService;
use Illuminate\Support\ServiceProvider;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ActivityLoggerService::class, fn($app) => new ActivityLoggerService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // No additional configuration needed for activity logger
    }
}
