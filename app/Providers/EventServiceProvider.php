<?php

namespace App\Providers;

use App\Events\Crud\EntityCreated;
use App\Events\Crud\EntityDeleted;
use App\Events\Crud\EntityUpdated;
use App\Listeners\Crud\LogEntityCreation;
use App\Listeners\Crud\LogEntityDeletion;
use App\Listeners\Crud\LogEntityUpdate;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogFailedLoginAttempt;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Failed::class => [
            LogFailedLoginAttempt::class,
        ],
        EntityCreated::class => [
            LogEntityCreation::class,
        ],
        EntityUpdated::class => [
            LogEntityUpdate::class,
        ],
        EntityDeleted::class => [
            LogEntityDeletion::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
} 