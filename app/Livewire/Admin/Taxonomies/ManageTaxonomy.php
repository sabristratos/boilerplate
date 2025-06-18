<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Taxonomies;

use App\Models\Taxonomy;
use App\Services\TaxonomyService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageTaxonomy extends Component
{
    public ?Taxonomy $taxonomy = null;

    public array $name = [];
    public array $description = [];
    public bool $hierarchical = false;
    public array $locales;
    public string $currentLocale;

    public function mount(?Taxonomy $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
        $this->locales = config('app.available_locales', ['en' => 'English']);
        $this->currentLocale = array_key_first($this->locales);

        if ($this->taxonomy?->exists) {
            $this->authorize('update', $this->taxonomy);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = $this->taxonomy->getTranslation('name', $localeCode);
                $this->description[$localeCode] = $this->taxonomy->getTranslation('description', $localeCode);
            }
            $this->hierarchical = $this->taxonomy->hierarchical;
        } else {
            $this->authorize('create', Taxonomy::class);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = '';
                $this->description[$localeCode] = '';
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'hierarchical' => 'boolean',
        ];

        $defaultLocale = config('app.fallback_locale');

        foreach (array_keys($this->locales) as $localeCode) {
            $rules["name.{$localeCode}"] = $localeCode === $defaultLocale
                ? 'required|string|max:255'
                : 'nullable|string|max:255';
            
            $rules["description.{$localeCode}"] = 'nullable|string|max:255';
        }

        return $rules;
    }

    protected function messages(): array
    {
        $messages = [];

        $defaultLocale = config('app.fallback_locale');

        foreach ($this->locales as $localeCode => $localeName) {
            if ($localeCode === $defaultLocale) {
                $messages["name.{$localeCode}.required"] = __('The name field is required for :locale.', ['locale' => $localeName]);
            }
            
            $messages["name.{$localeCode}.max"] = __('The name field must not exceed 255 characters for :locale.', ['locale' => $localeName]);
            $messages["description.{$localeCode}.max"] = __('The description field must not exceed 255 characters for :locale.', ['locale' => $localeName]);
        }

        return $messages;
    }

    public function save(TaxonomyService $taxonomyService): void
    {
        $this->resetErrorBag();

        try {
            $validated = $this->validate();

            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'hierarchical' => $validated['hierarchical'],
            ];

            if ($this->taxonomy?->exists) {
                $taxonomyService->update($this->taxonomy, $data);
                Flux::toast(
                    heading: __('Success'),
                    text: __('Taxonomy updated successfully.'),
                    variant: 'success'
                );

                $this->dispatch('taxonomy-saved');
            } else {
                $taxonomy = $taxonomyService->create($data);
                Flux::toast(
                    heading: __('Success'),
                    text: __('Taxonomy created successfully.'),
                    variant: 'success'
                );

                $this->redirect(
                    route('admin.taxonomies.edit', ['taxonomy' => $taxonomy->id]),
                    navigate: true
                );
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Flux::toast(
                heading: __('Validation Error'),
                text: __('Please check the form for errors.'),
                variant: 'danger'
            );
            throw $e;
        } catch (\Exception $e) {
            Log::error('Failed to save taxonomy', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Flux::toast(
                heading: __('Error'),
                text: __('Failed to save taxonomy. Please try again.'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomies.manage-taxonomy');
    }
} 