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

    protected function availableLocalesAsString(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => collect($this->getTranslatedLocales('title'))
            ->filter()
            ->implode(', '));
    }

    protected function firstAvailableLocale(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $locales = $this->getTranslatedLocales('title');
            return array_shift($locales);
        });
    }

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }
}
