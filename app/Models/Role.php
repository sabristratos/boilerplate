<?php

namespace App\Models;

use App\Interfaces\HasPermissions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Role extends Model implements HasPermissions
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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The users that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * The permissions that belong to the role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Check if the role has the given permission.
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
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
}
