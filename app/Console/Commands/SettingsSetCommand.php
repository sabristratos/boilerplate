<?php

namespace App\Console\Commands;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Console\Command;

class SettingsSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:set {key} {value}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a specific setting by key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $value = $this->argument('value');

        $setting = Setting::where('key', $key)->first();

        if (!$setting) {
            $this->error("Setting with key '{$key}' not found.");
            return;
        }

        $this->updateSettingValue($setting, $value);

        $this->info("Setting '{$key}' updated successfully.");
    }

    protected function updateSettingValue(Setting $setting, $value): void
    {
        $newValue = match ($setting->type) {
            SettingType::BOOLEAN, SettingType::CHECKBOX => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            SettingType::NUMBER => filter_var($value, FILTER_VALIDATE_INT),
            SettingType::MULTISELECT => json_decode($value, true),
            default => $value,
        };

        if ($newValue === null && in_array($setting->type, [SettingType::MULTISELECT])) {
            $this->error('Invalid JSON provided for multiselect setting.');
            return;
        }

        $setting->update(['value' => $newValue]);
    }
}
