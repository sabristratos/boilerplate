<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Taxonomy extends Model
{
    use HasFactory;
    use HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'hierarchical',
    ];

    /**
     * Get the terms for the taxonomy.
     */
    public function terms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the root terms for the taxonomy (terms without a parent).
     */
    public function rootTerms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->terms()->whereNull('parent_id');
    }

    /**
     * Scope a query to find a taxonomy by its slug.
     */
    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
    /**
     * Get the available locales as a string.
     */
    protected function availableLocalesAsString(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => collect($this->getTranslatedLocales('name'))
            ->filter()
            ->implode(', '));
    }
    /**
     * Get the first available locale.
     */
    protected function firstAvailableLocale(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $locales = $this->getTranslatedLocales('name');
            return array_shift($locales);
        });
    }
    protected function casts(): array
    {
        return [
            'hierarchical' => 'boolean',
        ];
    }
}
