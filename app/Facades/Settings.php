<?php

namespace App\Facades;

use App\Services\SettingsService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection all()
 * @method static mixed get(string $key, mixed $default = null)
 * @method static bool set(string $key, mixed $value)
 * @method static \Illuminate\Support\Collection group(string $groupSlug)
 * @method static \Illuminate\Support\Collection public()
 * @method static void clearCache()
 * @method static \Illuminate\Support\Collection allGroups()
 *
 * @see \App\Services\SettingsService
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SettingsService::class;
    }
}
