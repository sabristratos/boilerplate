<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLoggerService
{
    /**
     * Log an activity.
     *
     * @param string $description The description of the activity
     * @param Model|null $subject The subject of the activity
     * @param Model|null $causer The causer of the activity (defaults to current user)
     * @param string|null $event The event name
     * @param array $properties Additional properties to log
     * @param string|null $logName The name of the log
     */
    public function log(
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        ?string $event = null,
        array $properties = [],
        ?string $logName = 'default'
    ): ActivityLog {
        $causer ??= Auth::user();

        $data = [
            'log_name' => $logName,
            'description' => $description,
            'event' => $event,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ];

        if ($subject instanceof \Illuminate\Database\Eloquent\Model) {
            $data['subject_type'] = $subject::class;
            $data['subject_id'] = $subject->getKey();
        }

        if ($causer) {
            $data['causer_type'] = $causer::class;
            $data['causer_id'] = $causer->getKey();
        }

        return ActivityLog::create($data);
    }

    /**
     * Log a created event.
     *
     * @param Model $model The model that was created
     * @param Model|null $causer The causer of the activity (defaults to current user)
     * @param array $properties Additional properties to log
     * @param string|null $logName The name of the log
     */
    public function logCreated(
        Model $model,
        ?Model $causer = null,
        array $properties = [],
        ?string $logName = null
    ): ActivityLog {
        $modelName = class_basename($model);
        $logName ??= strtolower($modelName);

        return $this->log(
            "{$modelName} created",
            $model,
            $causer,
            'created',
            $properties,
            $logName
        );
    }

    /**
     * Log an updated event.
     *
     * @param Model $model The model that was updated
     * @param Model|null $causer The causer of the activity (defaults to current user)
     * @param array $properties Additional properties to log
     * @param string|null $logName The name of the log
     */
    public function logUpdated(
        Model $model,
        ?Model $causer = null,
        array $properties = [],
        ?string $logName = null
    ): ActivityLog {
        $modelName = class_basename($model);
        $logName ??= strtolower($modelName);

        return $this->log(
            "{$modelName} updated",
            $model,
            $causer,
            'updated',
            $properties,
            $logName
        );
    }

    /**
     * Log a deleted event.
     *
     * @param Model $model The model that was deleted
     * @param Model|null $causer The causer of the activity (defaults to current user)
     * @param array $properties Additional properties to log
     * @param string|null $logName The name of the log
     */
    public function logDeleted(
        Model $model,
        ?Model $causer = null,
        array $properties = [],
        ?string $logName = null
    ): ActivityLog {
        $modelName = class_basename($model);
        $logName ??= strtolower($modelName);

        return $this->log(
            "{$modelName} deleted",
            $model,
            $causer,
            'deleted',
            $properties,
            $logName
        );
    }

    /**
     * Log a custom event.
     *
     * @param string $event The event name
     * @param string $description The description of the activity
     * @param Model|null $subject The subject of the activity
     * @param Model|null $causer The causer of the activity (defaults to current user)
     * @param array $properties Additional properties to log
     * @param string|null $logName The name of the log
     */
    public function logCustom(
        string $event,
        string $description,
        ?Model $subject = null,
        ?Model $causer = null,
        array $properties = [],
        ?string $logName = 'custom'
    ): ActivityLog {
        return $this->log(
            $description,
            $subject,
            $causer,
            $event,
            $properties,
            $logName
        );
    }
}
