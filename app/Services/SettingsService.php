<?php

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\Admin\SettingChangedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class SettingsService
{
    /**
     * Cache key for all settings
     */
    public const CACHE_KEY = 'settings';

    /**
     * Cache expiration time in seconds (1 hour)
     */
    protected const CACHE_TTL = 3600;

    /**
     * Get all settings
     */
    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn() => Setting::all()->keyBy('key'));
    }

    /**
     * Get a setting by key
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
     */
    public function set(string $key, mixed $value): bool
    {
        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            return false;
        }

        $oldValue = $setting->value;
        $setting->value = $value;

        if (!$setting->isDirty('value')) {
            return true;
        }

        $result = $setting->save();

        if ($result) {
            $causer = auth()->user();
            if ($causer) {
                ActivityLogger::logUpdated(
                    $setting,
                    $causer,
                    ['old' => $oldValue, 'new' => $value]
                );

                // Notify all admin users
                $admins = User::whereHas('roles', fn ($query) => $query->where('slug', config('roles.super_admin')))->get();
                Notification::send($admins, new SettingChangedNotification($setting, $causer, $oldValue, $value));
            }
        }

        // Clear the cache
        $this->clearCache();

        return $result;
    }

    /**
     * Get all settings in a specific group
     */
    public function group(string $groupSlug): Collection
    {
        return Cache::remember("settings.group.{$groupSlug}", self::CACHE_TTL, fn() => Setting::whereHas('group', function ($query) use ($groupSlug) {
            $query->where('slug', $groupSlug);
        })->get());
    }

    /**
     * Get all public settings
     */
    public function public(): Collection
    {
        return Cache::remember('settings.public', self::CACHE_TTL, fn() => Setting::where('is_public', true)->get()->keyBy('key'));
    }

    /**
     * Clear the settings cache
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
     */
    public function allGroups(): Collection
    {
        return Cache::remember('settings.groups', self::CACHE_TTL, fn() => \App\Models\SettingGroup::with(['settings' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get());
    }
}
