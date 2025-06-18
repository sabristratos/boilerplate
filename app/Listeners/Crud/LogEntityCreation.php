<?php

namespace App\Listeners\Crud;

use App\Events\Crud\EntityCreated;
use App\Facades\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogEntityCreation
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
    public function handle(EntityCreated $event): void
    {
        ActivityLogger::logCreated($event->model, $event->causer);
    }
} 