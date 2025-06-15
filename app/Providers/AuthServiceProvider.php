<?php

namespace App\Providers;

use App\Actions\Auth\Logout;
use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\LegalPage;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use App\Policies\ActivityLogPolicy;
use App\Policies\AttachmentPolicy;
use App\Policies\LegalPagePolicy;
use App\Policies\NotificationPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TaxonomyPolicy;
use App\Policies\TermPolicy;
use App\Policies\UserPolicy;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Notifications\DatabaseNotification;
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
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Taxonomy::class => TaxonomyPolicy::class,
        Term::class => TermPolicy::class,
        Setting::class => SettingPolicy::class,
        Attachment::class => AttachmentPolicy::class,
        ActivityLog::class => ActivityLogPolicy::class,
        DatabaseNotification::class => NotificationPolicy::class,
        LegalPage::class => LegalPagePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Super Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        Gate::before(function ($user, $ability) {
            if ($user->hasRole(config('roles.super_admin'))) {
                return true;
            }

            return $user->hasPermission($ability) ? true : null;
        });

        Gate::define('edit-role', function (User $user, Role $role) {
            return $user->hasRole(config('roles.super_admin')) &&
                $role->slug !== config('roles.super_admin');
        });

        Gate::define('delete-role', function (User $user, Role $role) {
            return $user->hasRole(config('roles.super_admin')) &&
                $role->slug !== config('roles.super_admin');
        });

        Gate::define('edit-user', function (User $user, User $target) {
            return $user->hasRole(config('roles.super_admin')) &&
                !$target->hasRole(config('roles.super_admin'));
        });

        Gate::define('delete-user', function (User $user, User $target) {
            return $user->hasRole(config('roles.super_admin')) &&
                !$target->hasRole(config('roles.super_admin'));
        });

        Gate::define('impersonate', function (User $user, User $target) {
            return $user->hasRole(config('roles.super_admin')) &&
                   !$target->hasRole(config('roles.super_admin')) &&
                   $user->id !== $target->id;
        });

        // Bind the StatefulGuard to the Auth facade's guard for the Logout action
        $this->app->when(Logout::class)
            ->needs(StatefulGuard::class)
            ->give(fn () => Auth::guard());
    }
}
