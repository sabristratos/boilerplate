<?php

namespace App\Exports;

use App\Models\Translation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TranslationsExport implements FromCollection, WithHeadings
{
    public function __construct(protected array $locales, protected ?string $search = null)
    {
    }

    public function collection()
    {
        // 1. Discover all keys from language files
        $allKeys = $this->discoverAllKeysFromFiles();

        // 2. Query the database for existing translations
        $dbTranslations = Translation::whereIn('key', $allKeys)
            ->whereIn('locale', $this->locales)
            ->get()
            ->groupBy('key');

        // 3. Merge and build the final collection
        $exportCollection = new Collection();
        foreach ($allKeys as $key) {
            // Apply search filter
            if ($this->search && !str_contains((string) $key, $this->search)) {
                $foundInValue = false;
                foreach ($this->locales as $locale) {
                    $value = $dbTranslations->get($key)?->firstWhere('locale', $locale)?->value ?? '';
                    if (str_contains($value, $this->search)) {
                        $foundInValue = true;
                        break;
                    }
                }
                if (!$foundInValue) {
                    continue;
                }
            }

            $row = ['key' => $key];
            foreach ($this->locales as $locale) {
                $row[$locale] = $dbTranslations->get($key)?->firstWhere('locale', $locale)?->value ?? '';
            }
            $exportCollection->push($row);
        }

        return $exportCollection;
    }

    protected function discoverAllKeysFromFiles(): array
    {
        $allKeys = [];
        $langPath = lang_path();
        $jsonFiles = File::files($langPath);

        foreach ($jsonFiles as $file) {
            if ($file->getExtension() === 'json') {
                $content = json_decode($file->getContents(), true);
                $allKeys = array_merge($allKeys, array_keys($content));
            }
        }

        return array_unique($allKeys);
    }

    public function headings(): array
    {
        return array_merge(['key'], $this->locales);
    }
} 