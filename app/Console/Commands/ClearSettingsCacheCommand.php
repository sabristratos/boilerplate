<?php

namespace App\Console\Commands;

use App\Services\SettingsService;
use Illuminate\Console\Command;

class ClearSettingsCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:clear-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the settings cache.';

    /**
     * Execute the console command.
     */
    public function handle(SettingsService $settingsService): void
    {
        $this->info('Clearing settings cache...');

        $settingsService->clearCache();

        $this->info('Settings cache cleared successfully.');
    }
}
