<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Taxonomies;

use App\Models\Taxonomy;
use App\Services\TaxonomyService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageTaxonomy extends Component
{
    public ?Taxonomy $taxonomy = null;
    public string $name = '';
    public string $description = '';
    public bool $hierarchical = false;

    public function mount(?Taxonomy $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
        if ($this->taxonomy?->exists) {
            Gate::authorize('edit-taxonomies');
            $this->name = $this->taxonomy->name;
            $this->description = $this->taxonomy->description ?? '';
            $this->hierarchical = $this->taxonomy->hierarchical;
        } else {
            Gate::authorize('create-taxonomies');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('taxonomies', 'name')->ignore($this->taxonomy?->id),
            ],
            'description' => 'nullable|string|max:255',
            'hierarchical' => 'boolean',
        ];
    }

    public function save(TaxonomyService $taxonomyService): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'hierarchical' => $this->hierarchical,
        ];

        try {
            if ($this->taxonomy?->exists) {
                Gate::authorize('edit-taxonomies');
                $taxonomyService->updateTaxonomy($this->taxonomy, $data);
                Flux::toast(
                    text: __('Taxonomy updated successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
            } else {
                Gate::authorize('create-taxonomies');
                $taxonomy = $taxonomyService->createTaxonomy($data);
                Flux::toast(
                    text: __('Taxonomy created successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
                $this->redirect(route('admin.taxonomies.edit', $taxonomy), navigate: true);

                return;
            }
        } catch (\Exception $e) {
            Log::error('Failed to save taxonomy: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to save taxonomy. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );

            return;
        }

        $this->dispatch('taxonomy-saved');
        $this->redirect(route('admin.taxonomies.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.taxonomies.manage-taxonomy');
    }
} 