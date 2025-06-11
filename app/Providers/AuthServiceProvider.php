<?php

namespace App\Providers;

use App\Actions\Auth\Logout;
use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

        // This callback is checked before any other Gate checks.
        Gate::before(function (User $user, string $permission) {
            // If the user has the permission, return true.
            // The hasPermission method should handle all permission logic,
            // including checking roles that have the permission.
            return $user->hasPermission($permission) ? true : null;
        });

        Gate::define('impersonate', function (User $user, User $target) {
            return $user->hasRole('admin') &&
                   !$target->hasRole('admin') &&
                   $user->id !== $target->id;
        });

        // Bind the StatefulGuard to the Auth facade's guard for the Logout action
        $this->app->when(Logout::class)
            ->needs(StatefulGuard::class)
            ->give(fn () => Auth::guard());
    }
}
