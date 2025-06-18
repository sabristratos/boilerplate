<?php

namespace App\Listeners\Crud;

use App\Events\Crud\EntityUpdated;
use App\Facades\ActivityLogger;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogEntityUpdate
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
    public function handle(EntityUpdated $event): void
    {
        ActivityLogger::logUpdated($event->model, $event->causer, [
            'old' => $event->oldData,
            'new' => $event->model->getDirty(),
        ]);
    }
} 