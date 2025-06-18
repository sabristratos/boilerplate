<?php

namespace App\Listeners\Crud;

use App\Events\Crud\EntityDeleted;
use App\Facades\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogEntityDeletion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EntityDeleted $event): void
    {
        ActivityLogger::logDeleted($event->model, $event->causer);
    }
} 