<?php

namespace App\Models;

use App\Enums\SettingType;
use App\Models\Attachment;
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
        'validation_rules',
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
        switch ($this->type) {
            case SettingType::BOOLEAN:
            case SettingType::CHECKBOX:
                return (bool) $this->value;
            case SettingType::NUMBER:
                return (int) $this->value;
            case SettingType::JSON:
                return json_decode($this->value, true);
            case SettingType::FILE:
                $attachment = Attachment::find($this->value);

                return $attachment ? $attachment->url . '?v=' . $attachment->updated_at->timestamp : null;
            default:
                return $this->value;
        }
    }

    /**
     * Get the translated options for the current locale.
     *
     * @return array
     */
    public function getTranslatedOptionsAttribute(): array
    {
        $locale = app()->getLocale();
        $options = $this->getTranslation('options', $locale);

        if (is_array($options)) {
            return $options;
        }

        // Fallback for older format
        $allOptions = $this->getTranslations('options');
        if (isset($allOptions[$locale])) {
            return $allOptions[$locale];
        }

        return $allOptions['en'] ?? [];
    }
}
