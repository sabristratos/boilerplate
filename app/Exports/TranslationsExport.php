<?php

namespace App\Exports;

use App\Models\Translation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TranslationsExport implements FromCollection, WithHeadings
{
    protected array $locales;
    protected ?string $search;

    public function __construct(array $locales, ?string $search = null)
    {
        $this->locales = $locales;
        $this->search = $search;
    }

    public function collection()
    {
        $keys = Translation::query()
            ->select('key')
            ->when($this->search, fn ($q) => $q->where('key', 'like', '%' . $this->search . '%')
                ->orWhere('value', 'like', '%' . $this->search . '%')
            )
            ->distinct()
            ->pluck('key');

        $allTranslations = new Collection();

        $keyChunks = $keys->chunk(500);

        foreach ($keyChunks as $chunkedKeys) {
            $translations = Translation::whereIn('key', $chunkedKeys)
                ->whereIn('locale', $this->locales)
                ->get()
                ->groupBy('key');

            $mapped = $chunkedKeys->map(function ($key) use ($translations) {
                $row = ['original_value' => $key];
                $keyTranslations = $translations->get($key);

                foreach ($this->locales as $locale) {
                    $translation = $keyTranslations?->firstWhere('locale', $locale);
                    $row[$locale] = $translation?->value ?? '';
                }

                return $row;
            });

            $allTranslations = $allTranslations->concat($mapped);
        }

        return $allTranslations;
    }

    public function headings(): array
    {
        return array_merge(['original_value'], $this->locales);
    }
} 