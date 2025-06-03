<?php

namespace App\Facades;

use App\Services\ActivityLoggerService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Models\ActivityLog log(string $description, ?\Illuminate\Database\Eloquent\Model $subject = null, ?\Illuminate\Database\Eloquent\Model $causer = null, ?string $event = null, array $properties = [], ?string $logName = 'default')
 * @method static \App\Models\ActivityLog logCreated(\Illuminate\Database\Eloquent\Model $model, ?\Illuminate\Database\Eloquent\Model $causer = null, array $properties = [], ?string $logName = null)
 * @method static \App\Models\ActivityLog logUpdated(\Illuminate\Database\Eloquent\Model $model, ?\Illuminate\Database\Eloquent\Model $causer = null, array $properties = [], ?string $logName = null)
 * @method static \App\Models\ActivityLog logDeleted(\Illuminate\Database\Eloquent\Model $model, ?\Illuminate\Database\Eloquent\Model $causer = null, array $properties = [], ?string $logName = null)
 * @method static \App\Models\ActivityLog logCustom(string $event, string $description, ?\Illuminate\Database\Eloquent\Model $subject = null, ?\Illuminate\Database\Eloquent\Model $causer = null, array $properties = [], ?string $logName = 'custom')
 *
 * @see \App\Services\ActivityLoggerService
 */
class ActivityLogger extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ActivityLoggerService::class;
    }
}
