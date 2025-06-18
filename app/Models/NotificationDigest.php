<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationDigest extends Model
{
    use HasFactory;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'notification_type',
        'data',
        'frequency',
    ];

    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
