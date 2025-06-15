<?php

namespace App\Livewire\Admin\Translations;

use App\Facades\Settings;
use App\Models\Translation;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
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
        $this->allLocales = json_decode(Settings::get('available_languages', '[]'), true) ?: [];
        if (empty($this->selectedLocales)) {
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

        foreach ($this->translationsData as $key => $values) {
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
        $localesToExport = !empty($this->selectedLocales) ? $this->selectedLocales : $this->allLocales;

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
        $keysPaginator = Translation::query()
            ->select('key')
            ->when($this->search, fn ($q) => $q->where('key', 'like', '%' . $this->search . '%')
                ->orWhere('value', 'like', '%' . $this->search . '%')
            )
            ->distinct()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $keys = $keysPaginator->pluck('key');

        $localesToFetch = array_unique(array_merge($this->selectedLocales, [$this->defaultLocale]));

        $translationsOnPage = Translation::query()
            ->whereIn('key', $keys)
            ->whereIn('locale', $localesToFetch)
            ->get();

        $groupedTranslations = $translationsOnPage->groupBy('key');

        $this->translationsData = [];
        foreach ($keys as $key) {
            foreach ($localesToFetch as $locale) {
                $translation = $groupedTranslations->get($key)?->firstWhere('locale', $locale);
                $this->translationsData[$key][$locale] = $translation->value ?? '';
            }
        }

        return view('livewire.admin.translations.manage-translations', [
            'keys' => $keysPaginator,
        ])->layout('components.layouts.admin');
    }
}
