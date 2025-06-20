<?php

namespace App\Providers;

use App\Services\PermissionService;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionService::class, fn($app) => new PermissionService());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
