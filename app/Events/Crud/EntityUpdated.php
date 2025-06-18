<?php

namespace App\Events\Crud;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class EntityUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Model $model;
    public ?User $causer;
    public array $oldData;

    /**
     * Create a new event instance.
     *
     * @param Model $model
     * @param User|null $causer
     * @param array $oldData
     */
    public function __construct(Model $model, ?User $causer, array $oldData)
    {
        $this->model = $model;
        $this->causer = $causer;
        $this->oldData = $oldData;
    }
} 