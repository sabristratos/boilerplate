<?php

namespace App\Models;

use App\Enums\SettingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = ['display_name', 'description', 'options'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'setting_group_id',
        'key',
        'display_name',
        'description',
        'value',
        'type',
        'options',
        'is_public',
        'is_required',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
        'is_required' => 'boolean',
        'type' => SettingType::class,
    ];

    /**
     * Get the group that owns the setting.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(SettingGroup::class, 'setting_group_id');
    }

    /**
     * Get the formatted value based on the setting type.
     *
     * @return mixed
     */
    public function getFormattedValueAttribute()
    {
        return match ($this->type) {
            SettingType::BOOLEAN, SettingType::CHECKBOX => (bool) $this->value,
            SettingType::NUMBER => (int) $this->value,
            SettingType::JSON => json_decode($this->value, true),
            default => $this->value,
        };
    }
}
