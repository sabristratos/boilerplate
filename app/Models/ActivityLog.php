<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'log_name',
        'description',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'event',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the subject of the activity.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity.
     */
    public function causer()
    {
        return $this->morphTo();
    }

    /**
     * Scope a query to only include activities with a specific log name.
     */
    public function scopeInLog($query, string $logName)
    {
        return $query->where('log_name', $logName);
    }

    /**
     * Scope a query to only include activities for a specific subject.
     */
    public function scopeForSubject($query, Model $subject)
    {
        return $query
            ->where('subject_type', get_class($subject))
            ->where('subject_id', $subject->getKey());
    }

    /**
     * Scope a query to only include activities caused by a specific model.
     */
    public function scopeCausedBy($query, Model $causer)
    {
        return $query
            ->where('causer_type', get_class($causer))
            ->where('causer_id', $causer->getKey());
    }

    /**
     * Scope a query to only include activities with a specific event.
     */
    public function scopeWithEvent($query, string $event)
    {
        return $query->where('event', $event);
    }
}
