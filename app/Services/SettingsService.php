<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache key for all settings
     */
    protected const CACHE_KEY = 'settings';

    /**
     * Cache expiration time in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get all settings
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return Setting::all()->keyBy('key');
        });
    }

    /**
     * Get a setting by key
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->all()->get($key);

        if (!$setting) {
            return $default;
        }

        return $setting->formatted_value;
    }

    /**
     * Set a setting value
     *
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public function set(string $key, mixed $value): bool
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        $setting->value = $value;
        $result = $setting->save();

        // Clear the cache
        $this->clearCache();

        return $result;
    }

    /**
     * Get all settings in a specific group
     *
     * @param string $groupSlug
     * @return Collection
     */
    public function group(string $groupSlug): Collection
    {
        return Cache::remember("settings.group.{$groupSlug}", self::CACHE_TTL, function () use ($groupSlug) {
            return Setting::whereHas('group', function ($query) use ($groupSlug) {
                $query->where('slug', $groupSlug);
            })->get();
        });
    }

    /**
     * Get all public settings
     *
     * @return Collection
     */
    public function public(): Collection
    {
        return Cache::remember('settings.public', self::CACHE_TTL, function () {
            return Setting::where('is_public', true)->get()->keyBy('key');
        });
    }

    /**
     * Clear the settings cache
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);

        // Clear group caches
        $groupSlugs = Setting::with('group')
            ->get()
            ->pluck('group.slug')
            ->unique();

        foreach ($groupSlugs as $slug) {
            Cache::forget("settings.group.{$slug}");
        }

        Cache::forget('settings.public');
        Cache::forget('settings.groups');
    }

    /**
     * Get all setting groups with their settings
     *
     * @return Collection
     */
    public function allGroups(): Collection
    {
        return Cache::remember('settings.groups', self::CACHE_TTL, function () {
            return \App\Models\SettingGroup::with(['settings' => function ($query) {
                $query->orderBy('order');
            }])->orderBy('order')->get();
        });
    }
}
