<?php

namespace App\Facades;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void send(mixed $users, mixed $notification)
 * @method static array getChannels(\App\Models\User $user = null)
 *
 * @see \App\Services\NotificationService
 */
class Notifications extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return NotificationService::class;
    }
}
