<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\SettingGroup;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SettingsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize settings from the configuration file to the database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting settings synchronization...');

        $this->syncSettingGroups();
        $this->syncSettings();

        $this->line('Clearing caches...');

        // Clear the settings cache to apply changes immediately.
        Artisan::call('cache:clear-settings');
        $this->info('Settings cache cleared.');

        // Clear the main application config cache.
        Artisan::call('config:clear');
        $this->info('Configuration cache cleared.');

        $this->info('Settings synchronization completed successfully.');
        return Command::SUCCESS;
    }

    /**
     * Synchronize setting groups from the config file to the database.
     */
    protected function syncSettingGroups(): void
    {
        $this->line('Syncing setting groups...');
        $groupsFromConfig = collect(config('settings.groups', []));
        $groupSlugsInConfig = $groupsFromConfig->pluck('slug')->all();

        // Create or update groups from config
        foreach ($groupsFromConfig as $groupData) {
            SettingGroup::updateOrCreate(
                ['slug' => $groupData['slug']],
                Arr::except($groupData, ['slug'])
            );
        }

        // Prune old groups
        $groupsToDelete = SettingGroup::whereNotIn('slug', $groupSlugsInConfig)->get();
        if ($groupsToDelete->isNotEmpty()) {
            $this->line('Pruning old setting groups...');
            foreach ($groupsToDelete as $group) {
                $this->warn("Deleting group: {$group->name} ({$group->slug})");
                $group->delete();
            }
        }

        $this->info('Setting groups synced.');
    }

    /**
     * Synchronize settings from the config file to the database.
     */
    protected function syncSettings(): void
    {
        $this->line('Syncing settings...');
        $settingsFromConfig = collect(config('settings.settings', []));
        $settingKeysInConfig = $settingsFromConfig->pluck('key')->all();

        foreach ($settingsFromConfig as $settingData) {
            $groupSlug = Arr::pull($settingData, 'group');
            $group = SettingGroup::where('slug', $groupSlug)->first();

            if (!$group) {
                $this->error("Setting group with slug '{$groupSlug}' not found for setting '{$settingData['key']}'. Skipping.");
                continue;
            }

            $defaultValue = Arr::pull($settingData, 'value');

            // Prepare the data for the setting record.
            // The value will only be set if the record is new.
            $data = array_merge($settingData, ['setting_group_id' => $group->id]);

            // Find the setting or create a new instance.
            $setting = Setting::firstOrNew(['key' => $settingData['key']]);

            // Fill the model with schema-related attributes.
            $setting->fill($data);

            // If the setting is new, set its default value.
            if (!$setting->exists) {
                $setting->value = $defaultValue;
            }

            $setting->save();
        }

        // Prune old settings
        $settingsToDelete = Setting::whereNotIn('key', $settingKeysInConfig)->get();
        if ($settingsToDelete->isNotEmpty()) {
            $this->line('Pruning old settings...');
            foreach ($settingsToDelete as $setting) {
                $this->warn("Deleting setting: {$setting->display_name} ({$setting->key})");
                $setting->delete();
            }
        }

        $this->info('Settings synced.');
    }
}
