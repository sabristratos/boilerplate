<?php

namespace App\Providers;

use App\Actions\Auth\Logout;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Bind the StatefulGuard to the Auth facade's guard for the Logout action
        $this->app->when(Logout::class)
            ->needs(StatefulGuard::class)
            ->give(fn () => Auth::guard());
    }
}
