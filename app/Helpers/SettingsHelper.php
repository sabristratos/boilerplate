<?php

use App\Facades\Settings;

if(!function_exists('setting')){
    /**
     * Get a setting value.
     * Leverages the SettingsService which includes caching.
     *
     * @param string $key The key of the setting.
     * @param mixed $default Optional default value if the setting is not found.
     * @return mixed The value of the setting or the default.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}

if(!function_exists('setting_is')){
    function setting_is(string $key, mixed $expectedValue): bool
    {
        return Settings::get($key) === $expectedValue;
    }
}
