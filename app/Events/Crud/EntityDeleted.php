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

    public Model $model;
    public ?User $causer;

    /**
     * Create a new event instance.
     *
     * @param Model $model
     * @param User|null $causer
     */
    public function __construct(Model $model, ?User $causer)
    {
        $this->model = $model;
        $this->causer = $causer;
    }
} 