<?php

namespace App\Events\Crud;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EntityDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Model $model, public ?User $causer)
    {
    }
} 