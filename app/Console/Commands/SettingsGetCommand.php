<?php

namespace App\Console\Commands;

use App\Facades\Settings;
use Illuminate\Console\Command;

class SettingsGetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:get {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get a specific setting by key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->argument('key');
        $setting = Settings::get($key);

        if ($setting === null) {
            $this->error("Setting with key '{$key}' not found.");
            return;
        }

        if (is_array($setting)) {
            $this->info(json_encode($setting, JSON_PRETTY_PRINT));
        } else {
            $this->info((string) $setting);
        }
    }
}
