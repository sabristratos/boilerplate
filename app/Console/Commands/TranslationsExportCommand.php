<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslationsExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translations from the database to language files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation export...');

        $translations = Translation::all()->groupBy('locale');

        foreach ($translations as $locale => $localeTranslations) {
            $filePath = lang_path($locale . '.json');
            $translationsArray = $localeTranslations->pluck('value', 'key')->toArray();

            // Ensure consistent pretty-printing
            $json = json_encode($translationsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            File::put($filePath, $json);

            $this->info("Exported translations for '{$locale}'.");
        }

        $this->info('Translation export completed successfully.');
    }
}
