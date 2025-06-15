<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class SettingsListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all application settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $settings = Setting::all(['key', 'value', 'type']);

        if ($settings->isEmpty()) {
            $this->info('No settings found.');
            return;
        }

        $this->table(
            ['Key', 'Value', 'Type'],
            $settings->map(function ($setting) {
                $value = $setting->value;
                if (is_array($value)) {
                    $value = json_encode($value);
                }
                if (strlen($value) > 50) {
                    $value = substr($value, 0, 50) . '...';
                }
                return [
                    'key' => $setting->key,
                    'value' => $value,
                    'type' => $setting->type->value,
                ];
            })
        );
    }
}
