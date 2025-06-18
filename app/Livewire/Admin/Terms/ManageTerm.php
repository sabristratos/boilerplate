<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Terms;

use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\TermService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageTerm extends Component
{
    public Taxonomy $taxonomy;
    public ?Term $term = null;

    public array $name = [];
    public array $description = [];
    public ?int $parentId = null;
    public array $locales;
    public string $currentLocale;

    public function mount(Taxonomy $taxonomy, ?Term $term): void
    {
        $this->taxonomy = $taxonomy;
        $this->term = $term;
        $this->locales = config('app.available_locales', ['en' => 'English']);
        $this->currentLocale = array_key_first($this->locales);

        if ($this->term?->exists) {
            $this->authorize('update', $this->term);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = $this->term->getTranslation('name', $localeCode);
                $this->description[$localeCode] = $this->term->getTranslation('description', $localeCode);
            }
            $this->parentId = $this->term->parent_id;
        } else {
            $this->authorize('create', [Term::class, $taxonomy]);
            foreach (array_keys($this->locales) as $localeCode) {
                $this->name[$localeCode] = '';
                $this->description[$localeCode] = '';
            }
        }
    }

    protected function rules(): array
    {
        $rules = [
            'parentId' => [
                'nullable',
                Rule::exists('terms', 'id')->where('taxonomy_id', $this->taxonomy->id),
            ],
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

        $messages['parentId.exists'] = __('The selected parent term is invalid.');

        return $messages;
    }

    public function save(TermService $termService): void
    {
        $this->resetErrorBag();

        try {
            $validated = $this->validate();

            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'parent_id' => $validated['parentId'],
                'taxonomy_id' => $this->taxonomy->id,
            ];

            if ($this->term?->exists) {
                $termService->update($this->term, $data);
                Flux::toast(
                    heading: __('Success'),
                    text: __('Term updated successfully.'),
                    variant: 'success'
                );

                $this->dispatch('term-saved');
            } else {
                $term = $termService->create($data);
                Flux::toast(
                    heading: __('Success'),
                    text: __('Term created successfully.'),
                    variant: 'success'
                );

                $this->redirect(
                    route('admin.taxonomies.terms.edit', ['taxonomy' => $this->taxonomy->id, 'term' => $term->id]),
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
            Log::error('Failed to save term', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Flux::toast(
                heading: __('Error'),
                text: __('Failed to save term. Please try again.'),
                variant: 'danger'
            );
        }
    }

    public function render(): View
    {
        return view('livewire.admin.terms.manage-term', [
            'parentTerms' => $this->taxonomy->hierarchical
                ? $this->taxonomy->terms()->where('id', '!=', $this->term?->id)->get()
                : collect(),
        ]);
    }
}
