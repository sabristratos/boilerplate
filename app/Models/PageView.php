<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Represents a single page view tracked by the analytics system.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $session_id
 * @property string $path
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $utm_source
 * @property string|null $utm_medium
 * @property string|null $utm_campaign
 * @property string|null $utm_term
 * @property string|null $utm_content
 * @property string|null $device_type
 * @property string|null $browser_name
 * @property string|null $platform_name
 * @property Carbon $visited_at
 *
 * @property string $date
 * @property int $views
 * @property-read User|null $user
 */
class PageView extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'path',
        'ip_address',
        'user_agent',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'device_type',
        'browser_name',
        'platform_name',
        'visited_at',
    ];

    /**
     * Get the user associated with the page view, if any.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visited_at' => 'datetime',
            'user_id' => 'integer',
        ];
    }
}
