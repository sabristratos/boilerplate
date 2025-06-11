<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Terms;

use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\TermService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageTerm extends Component
{
    public Taxonomy $taxonomy;
    public ?Term $term = null;

    public string $name = '';
    public string $description = '';
    public ?int $parent_id = null;

    public function mount(Taxonomy $taxonomy, ?Term $term): void
    {
        $this->taxonomy = $taxonomy;
        $this->term = $term;

        if ($this->term?->exists) {
            Gate::authorize('edit-terms');
            $this->name = $this->term->name;
            $this->description = $this->term->description ?? '';
            $this->parent_id = $this->term->parent_id;
        } else {
            Gate::authorize('create-terms');
        }
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('terms', 'name')
                    ->where('taxonomy_id', $this->taxonomy->id)
                    ->ignore($this->term?->id),
            ],
            'description' => 'nullable|string|max:255',
            'parent_id' => 'nullable|exists:terms,id',
        ];
    }

    public function save(TermService $termService): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'taxonomy_id' => $this->taxonomy->id,
            'parent_id' => $this->parent_id,
        ];

        try {
            if ($this->term?->exists) {
                Gate::authorize('edit-terms');
                $termService->updateTerm($this->term, $data);
                Flux::toast(
                    text: __('Term updated successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
            } else {
                Gate::authorize('create-terms');
                $term = $termService->createTerm($data);
                Flux::toast(
                    text: __('Term created successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
                $this->redirect(route('admin.taxonomies.terms.edit', [
                    'taxonomy' => $this->taxonomy,
                    'term' => $term,
                ]), navigate: true);

                return;
            }
        } catch (\Exception $e) {
            Log::error('Failed to save term: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to save term. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );

            return;
        }

        $this->dispatch('term-saved');
        $this->redirect(route('admin.taxonomies.terms.index', $this->taxonomy), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.terms.manage-term', [
            'parentTerms' => $this->taxonomy->terms()->where('id', '!=', $this->term?->id)->get(),
        ]);
    }
} 