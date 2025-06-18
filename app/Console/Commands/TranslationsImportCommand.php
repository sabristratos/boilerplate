<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslationsImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations from language files into the database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting translation import...');

        $locales = collect(File::directories(lang_path()))
            ->map(fn ($dir) => basename((string) $dir));

        if ($locales->isEmpty()) {
            // Fallback for when translations are in json files in the root of lang/
            $jsonFiles = glob(lang_path('*.json'));
            if ($jsonFiles !== [] && $jsonFiles !== false) {
                $locales = collect($jsonFiles)
                    ->map(fn ($file) => pathinfo($file, PATHINFO_FILENAME));
            }
        }

        foreach ($locales as $locale) {
            $filePath = lang_path($locale . '.json');
            if (!File::exists($filePath)) {
                $this->warn("Language file not found for locale '{$locale}'. Skipping.");
                continue;
            }

            $translations = json_decode(File::get($filePath), true);

            foreach ($translations as $key => $value) {
                Translation::updateOrCreate(
                    [
                        'locale' => $locale,
                        'key' => $key,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }

            $this->info("Imported translations for '{$locale}'.");
        }

        $this->info('Translation import completed successfully.');
    }
}
