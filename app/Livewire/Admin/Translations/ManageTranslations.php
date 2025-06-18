<?php

namespace App\Livewire\Admin\Translations;

use App\Facades\Settings;
use App\Models\Translation;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\TranslationsExport;
use App\Imports\TranslationsImport;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ManageTranslations extends Component
{
    use WithPagination, WithFileUploads;

    public ?string $search = null;

    public int $perPage = 10;

    public string $sortBy = 'key';

    public string $sortDirection = 'asc';

    public array $allLocales = [];

    public array $selectedLocales = [];

    public array $translationsData = [];

    public array $paginatedKeysArray = [];

    public string $defaultLocale;

    public $file;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortBy' => ['except' => 'key'],
        'sortDirection' => ['except' => 'asc'],
        'selectedLocales' => ['except' => []],
    ];

    public function mount(): void
    {
        $this->defaultLocale = config('app.locale');
        $this->allLocales = json_decode((string) Settings::get('available_languages', '[]'), true) ?: [];
        if ($this->selectedLocales === []) {
            $this->selectedLocales = $this->allLocales;
        }
    }

    public function updating($key): void
    {
        if (in_array($key, ['search', 'perPage', 'selectedLocales'])) {
            $this->resetPage();
        }
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function save(): void
    {
        $this->validate([
            'translationsData.*.*' => 'string|nullable',
        ]);

        foreach ($this->translationsData as $index => $values) {
            if (!isset($this->paginatedKeysArray[$index])) {
                continue;
            }
            $key = $this->paginatedKeysArray[$index];

            foreach ($values as $locale => $value) {
                Translation::updateOrCreate(
                    ['locale' => $locale, 'key' => $key],
                    ['value' => (string) $value]
                );
            }
        }

        Artisan::call('translations:export');

        Flux::toast(
            heading: __('Success'),
            text: __('Translations saved and exported successfully.'),
            variant: 'success'
        );
    }

    public function importFromFiles(): void
    {
        Artisan::call('translations:import');
        Flux::toast(
            heading: __('Success'),
            text: __('Translations imported successfully.'),
            variant: 'success'
        );
    }

    public function exportToFiles(): void
    {
        Artisan::call('translations:export');
        Flux::toast(
            heading: __('Success'),
            text: __('Translations exported successfully.'),
            variant: 'success'
        );
    }

    public function export()
    {
        $localesToExport = $this->selectedLocales === [] ? $this->allLocales : $this->selectedLocales;

        return Excel::download(new TranslationsExport($localesToExport, $this->search), 'translations.csv');
    }

    public function import(): void
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx'
        ]);

        Excel::import(new TranslationsImport, $this->file);

        Flux::toast(
            heading: __('Success'),
            text: __('Translations imported successfully.'),
            variant: 'success'
        );

        $this->file = null;
    }

    public function render(): View
    {
        $allKeys = $this->getAllKeys();
        $dbTranslations = Translation::query()
            ->whereIn('key', $allKeys)
            ->whereIn('locale', $this->selectedLocales)
            ->get()
            ->groupBy('key');
        $filteredKeys = $this->filterKeys($allKeys, $dbTranslations);
        $sortedKeys = $this->sortKeys($filteredKeys);
        $paginatedKeys = $this->paginateKeys($sortedKeys);

        $this->paginatedKeysArray = $paginatedKeys->items();

        $this->translationsData = [];
        foreach ($this->paginatedKeysArray as $index => $key) {
            foreach ($this->selectedLocales as $locale) {
                $this->translationsData[$index][$locale] = $dbTranslations->get($key)?->firstWhere('locale', $locale)?->value ?? '';
            }
        }

        return view('livewire.admin.translations.manage-translations', [
            'keys' => $paginatedKeys,
        ])->layout('components.layouts.admin');
    }

    private function getAllKeys(): array
    {
        $fileKeys = [];
        $langPath = lang_path();
        $jsonFiles = File::files($langPath);

        foreach ($jsonFiles as $file) {
            if ($file->getExtension() === 'json') {
                $content = json_decode($file->getContents(), true);
                $fileKeys = array_merge($fileKeys, array_keys($content));
            }
        }

        $databaseKeys = Translation::query()->distinct()->pluck('key')->toArray();

        return array_unique(array_merge($fileKeys, $databaseKeys));
    }

    private function filterKeys(array $keys, Collection $translations): array
    {
        if (! $this->search) {
            return $keys;
        }

        return array_filter($keys, function ($key) use ($translations) {
            if (str_contains(strtolower($key), strtolower((string) $this->search))) {
                return true;
            }

            if ($translationGroup = $translations->get($key)) {
                foreach ($translationGroup as $translation) {
                    if (str_contains(strtolower((string) $translation->value), strtolower((string) $this->search))) {
                        return true;
                    }
                }
            }

            return false;
        });
    }

    private function sortKeys(array $keys): Collection
    {
        $collection = new Collection($keys);

        return $collection->sortBy($this->sortBy, SORT_REGULAR, $this->sortDirection === 'desc');
    }

    private function paginateKeys(Collection $keys): LengthAwarePaginator
    {
        $currentPage = $this->getPage();
        $perPage = $this->perPage;

        $currentPageItems = $keys->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentPageItems,
            $keys->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
