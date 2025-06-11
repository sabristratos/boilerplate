<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class LegalPage extends Model
{
    use HasFactory;
    use HasTranslations;

    public array $translatable = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
    ];

    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_published',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function getAvailableLocalesAsStringAttribute(): string
    {
        return collect($this->getTranslatedLocales('title'))
            ->filter()
            ->implode(', ');
    }

    public function getFirstAvailableLocaleAttribute(): ?string
    {
        $locales = $this->getTranslatedLocales('title');
        return array_shift($locales);
    }
}
