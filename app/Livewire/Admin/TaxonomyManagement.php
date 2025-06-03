<?php

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Models\Taxonomy;
use App\Models\Term;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Flux\Flux;
use Illuminate\Validation\Rule as ValidationRule;

/**
 * Manages taxonomies and their terms within the admin panel using modals for CRUD operations.
 *
 * This component allows for creating, reading, updating, and deleting
 * taxonomies (e.g., categories, tags) and their associated terms.
 * It includes features like pagination, search, and hierarchical term management.
 */
#[Layout('components.admin-layout')]
class TaxonomyManagement extends Component
{
    use WithPagination;

    // Taxonomy form properties
    #[Rule('required|min:3|max:255')]
    public string $taxonomyName = '';
    #[Rule('nullable|max:255')]
    public string $taxonomyDescription = '';
    #[Rule('boolean')]
    public bool $taxonomyHierarchical = false;

    // Term form properties
    public ?int $selectedTaxonomyId = null;
    public ?Taxonomy $currentTaxonomy = null;

    #[Rule('required|min:3|max:255')]
    public string $termName = '';
    #[Rule('nullable|max:255')]
    public string $termDescription = '';
    public ?int $termParentId = null;
    public int $termOrder = 0;

    // Modal and state properties
    public bool $showModal = false;
    public bool $isCreatingTaxonomy = false;
    public bool $isEditingTaxonomy = false;
    public bool $isCreatingTerm = false;
    public bool $isEditingTerm = false;

    public ?int $editingTaxonomyId = null;
    public ?int $editingTermId = null;

    // Search and filter properties
    public string $search = '';
    public int $perPage = 10;

    /**
     * Get custom validation messages.
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'taxonomyName.required' => __('Taxonomy name is required.'),
            'taxonomyName.unique' => __('This taxonomy name already exists.'),
            'termName.required' => __('Term name is required.'),
            'termName.unique' => __('This term name already exists for the selected taxonomy.'),
        ];
    }

    /**
     * Resets pagination when the search query is updated.
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage('taxonomiesPage');
        if ($this->selectedTaxonomyId) {
            $this->resetPage('termsPage');
        }
    }

    /**
     * Resets pagination when the number of items per page is updated.
     * @return void
     */
    public function updatedPerPage(): void
    {
        $this->resetPage('taxonomiesPage');
        if ($this->selectedTaxonomyId) {
            $this->resetPage('termsPage');
        }
    }

    /**
     * Handles changes to the selectedTaxonomyId property.
     * Fetches the current taxonomy model and resets term editing state.
     * @param  int|null  $value
     * @return void
     */
    public function updatedSelectedTaxonomyId(?int $value): void
    {
        if ($value) {
            $this->currentTaxonomy = Taxonomy::find($value);
        } else {
            $this->currentTaxonomy = null;
        }
        $this->editingTermId = null; // Reset term editing if taxonomy changes
        $this->isCreatingTerm = false;
        $this->isEditingTerm = false;
        $this->resetPage('termsPage');
    }

    /**
     * Prepares and opens the modal for creating a new taxonomy.
     * @return void
     */
    public function openCreateTaxonomyModal(): void
    {
        $this->resetTaxonomyForm();
        $this->isCreatingTaxonomy = true;
        $this->showModal = true;
    }

    /**
     * Stores a new taxonomy.
     * @return void
     */
    public function storeTaxonomy(): void
    {
        $this->validate([
            'taxonomyName' => ['required', 'min:3', 'max:255', ValidationRule::unique('taxonomies', 'name')],
            'taxonomyDescription' => 'nullable|max:255',
            'taxonomyHierarchical' => 'boolean',
        ]);

        $slug = Str::slug($this->taxonomyName);
        if (Taxonomy::where('slug', $slug)->exists()) {
            $this->addError('taxonomyName', __('A taxonomy with this slug already exists. Please choose a different name.'));
            return;
        }

        try {
            $taxonomy = Taxonomy::create([
                'name' => $this->taxonomyName,
                'slug' => $slug,
                'description' => $this->taxonomyDescription,
                'hierarchical' => $this->taxonomyHierarchical,
            ]);
            ActivityLogger::logCreated($taxonomy, auth()->user(), $taxonomy->toArray(), 'taxonomy');
            Flux::toast(text: __('Taxonomy created successfully.'), heading: __('Success'), variant: 'success');
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error(__('Error creating taxonomy: ') . $e->getMessage());
            Flux::toast(text: __('Could not create taxonomy. Please try again.'), heading: __('Error'),
                variant: 'danger');
        }
    }

    /**
     * Prepares and opens the modal for editing an existing taxonomy.
     * @param Taxonomy $taxonomy The taxonomy to edit.
     * @return void
     */
    public function openEditTaxonomyModal(Taxonomy $taxonomy): void
    {
        $this->resetTaxonomyForm();
        $this->editingTaxonomyId = $taxonomy->id;
        $this->taxonomyName = $taxonomy->name;
        $this->taxonomyDescription = $taxonomy->description ?? '';
        $this->taxonomyHierarchical = $taxonomy->hierarchical;
        $this->isEditingTaxonomy = true;
        $this->showModal = true;
    }

    /**
     * Updates an existing taxonomy.
     * @return void
     */
    public function updateTaxonomy(): void
    {
        if (!$this->editingTaxonomyId) {
            Flux::toast(text: __('No taxonomy selected for update.'), heading: __('Error'), variant: 'danger');
            return;
        }
        $this->validate([
            'taxonomyName' => ['required', 'min:3', 'max:255', ValidationRule::unique('taxonomies', 'name')->ignore($this->editingTaxonomyId)],
            'taxonomyDescription' => 'nullable|max:255',
            'taxonomyHierarchical' => 'boolean',
        ]);

        try {
            $taxonomy = Taxonomy::findOrFail($this->editingTaxonomyId);
            $slug = Str::slug($this->taxonomyName);

            if (Taxonomy::where('slug', $slug)->where('id', '!=', $this->editingTaxonomyId)->exists()) {
                $this->addError('taxonomyName', __('A taxonomy with this slug already exists. Please choose a different name.'));
                return;
            }

            $oldValues = $taxonomy->getOriginal();
            $taxonomy->update([
                'name' => $this->taxonomyName,
                'slug' => $slug,
                'description' => $this->taxonomyDescription,
                'hierarchical' => $this->taxonomyHierarchical,
            ]);
            ActivityLogger::logUpdated($taxonomy, auth()->user(), ['old' => $oldValues, 'new' => $taxonomy->toArray()], 'taxonomy');
            Flux::toast(text: __('Taxonomy updated successfully.'), heading: __('Success'), variant: 'success');
            $this->closeModal();
            if ($this->selectedTaxonomyId == $taxonomy->id) {
                $this->currentTaxonomy = $taxonomy->fresh();
            }
        } catch (\Exception $e) {
            Log::error(__('Error updating taxonomy: ') . $e->getMessage());
            Flux::toast(text: __('Could not update taxonomy. Please try again.'), heading: __('Error'),
                variant: 'danger');
        }
    }

    /**
     * Deletes a taxonomy.
     * @param Taxonomy $taxonomy The taxonomy to delete.
     * @return void
     */
    public function deleteTaxonomy(Taxonomy $taxonomy): void
    {
        try {
            ActivityLogger::logDeleted($taxonomy, auth()->user(), $taxonomy->toArray(), 'taxonomy');
            $taxonomy->delete();
            Flux::toast(text: __('Taxonomy and its terms deleted successfully.'), heading: __('Success'),
                variant: 'success');
            if ($this->selectedTaxonomyId === $taxonomy->id) {
                $this->selectedTaxonomyId = null;
                $this->currentTaxonomy = null;
            }
            $this->resetPage('taxonomiesPage');
        } catch (\Exception $e) {
            Log::error(__('Error deleting taxonomy: ') . $e->getMessage());
            Flux::toast(text: __('Could not delete taxonomy.'), heading: __('Error'), variant: 'danger');
        }
    }

    /**
     * Prepares and opens the modal for creating a new term for the currently selected taxonomy.
     * @return void
     */
    public function openCreateTermModal(): void
    {
        if (!$this->selectedTaxonomyId) {
            Flux::toast(text: __('Please select a taxonomy first to add a term.'), heading: __('Error'),
                variant: 'warning');
            return;
        }
        $this->currentTaxonomy = Taxonomy::find($this->selectedTaxonomyId);
        if (!$this->currentTaxonomy) {
            Flux::toast(text: __('Selected taxonomy not found.'), heading: __('Error'), variant: 'danger');
            $this->selectedTaxonomyId = null;
            return;
        }
        $this->resetTermForm();
        $this->isCreatingTerm = true;
        $this->showModal = true;
    }

    /**
     * Stores a new term.
     * @return void
     */
    public function storeTerm(): void
    {
        if (!$this->selectedTaxonomyId) return;

        $this->validate([
            'termName' => ['required', 'min:3', 'max:255', ValidationRule::unique('terms', 'name')->where('taxonomy_id', $this->selectedTaxonomyId)],
            'termDescription' => 'nullable|max:255',
            'termParentId' => 'nullable|exists:terms,id',
            'termOrder' => 'integer|min:0',
        ]);

        try {
            $taxonomy = Taxonomy::findOrFail($this->selectedTaxonomyId);
            $slug = Str::slug($this->termName);
            if (Term::where('taxonomy_id', $this->selectedTaxonomyId)->where('slug', $slug)->exists()) {
                $this->addError('termName', __('A term with this slug already exists in this taxonomy.'));
                return;
            }
            if (!$taxonomy->hierarchical) $this->termParentId = null;

            $term = Term::create([
                'taxonomy_id' => $this->selectedTaxonomyId,
                'name' => $this->termName,
                'slug' => $slug,
                'description' => $this->termDescription,
                'parent_id' => $this->termParentId,
                'order' => $this->termOrder,
            ]);
            ActivityLogger::logCreated($term, auth()->user(), array_merge($term->toArray(), ['taxonomy_name' => $taxonomy->name]), 'term');
            Flux::toast(text: __('Term created successfully.'), heading: __('Success'), variant: 'success');
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error(__('Error creating term: ') . $e->getMessage());
            Flux::toast(text: __('Could not create term. Please try again.'), heading: __('Error'), variant: 'danger');
        }
    }

    /**
     * Prepares and opens the modal for editing an existing term.
     * @param Term $term The term to edit.
     * @return void
     */
    public function openEditTermModal(Term $term): void
    {
        $this->resetTermForm();
        $this->editingTermId = $term->id;
        $this->selectedTaxonomyId = $term->taxonomy_id; // Ensure context is set
        $this->currentTaxonomy = $term->taxonomy;
        $this->termName = $term->name;
        $this->termDescription = $term->description ?? '';
        $this->termParentId = $term->parent_id;
        $this->termOrder = $term->order;
        $this->isEditingTerm = true;
        $this->showModal = true;
    }

    /**
     * Updates an existing term.
     * @return void
     */
    public function updateTerm(): void
    {
        if (!$this->editingTermId || !$this->selectedTaxonomyId) {
            Flux::toast(text: __('No term selected for update.'), heading: __('Error'), variant: 'danger');
            return;
        }
        $this->validate([
            'termName' => ['required', 'min:3', 'max:255', ValidationRule::unique('terms', 'name')->where('taxonomy_id', $this->selectedTaxonomyId)->ignore($this->editingTermId)],
            'termDescription' => 'nullable|max:255',
            'termParentId' => 'nullable|exists:terms,id',
            'termOrder' => 'integer|min:0',
        ]);

        try {
            $term = Term::findOrFail($this->editingTermId);
            $taxonomy = Taxonomy::findOrFail($this->selectedTaxonomyId);
            $slug = Str::slug($this->termName);

            if (Term::where('taxonomy_id', $this->selectedTaxonomyId)->where('slug', $slug)->where('id', '!=', $this->editingTermId)->exists()) {
                $this->addError('termName', __('A term with this slug already exists in this taxonomy.'));
                return;
            }
            if (!$taxonomy->hierarchical) $this->termParentId = null;
            if ($this->termParentId == $this->editingTermId) {
                $this->addError('termParentId', __('A term cannot be its own parent.'));
                return;
            }

            $oldValues = $term->getOriginal();
            $term->update([
                'name' => $this->termName,
                'slug' => $slug,
                'description' => $this->termDescription,
                'parent_id' => $this->termParentId,
                'order' => $this->termOrder,
            ]);
            ActivityLogger::logUpdated($term, auth()->user(), ['old' => $oldValues, 'new' => $term->toArray(), 'taxonomy_name' => $taxonomy->name], 'term');
            Flux::toast(text: __('Term updated successfully.'), heading: __('Success'), variant: 'success');
            $this->closeModal();
        } catch (\Exception $e) {
            Log::error(__('Error updating term: ') . $e->getMessage());
            Flux::toast(text: __('Could not update term. Please try again.'), heading: __('Error'), variant: 'danger');
        }
    }

    /**
     * Deletes a term.
     * @param Term $term The term to delete.
     * @return void
     */
    public function deleteTerm(Term $term): void
    {
        if ($term->children()->exists()) {
            Flux::toast(text: __('Cannot delete a term with children. Please delete or reassign them first.'),
                heading: __('Error'), variant: 'danger');
            return;
        }
        try {
            ActivityLogger::logDeleted($term, auth()->user(), $term->toArray(), 'term');
            $term->delete();
            Flux::toast(text: __('Term deleted successfully.'), heading: __('Success'), variant: 'success');
        } catch (\Exception $e) {
            Log::error(__('Error deleting term: ') . $e->getMessage());
            Flux::toast(text: __('Could not delete term.'), heading: __('Error'), variant: 'danger');
        }
    }

    /**
     * Resets taxonomy form fields.
     * @return void
     */
    private function resetTaxonomyForm(): void
    {
        $this->reset(['taxonomyName', 'taxonomyDescription', 'taxonomyHierarchical', 'editingTaxonomyId']);
        $this->isCreatingTaxonomy = false;
        $this->isEditingTaxonomy = false;
        $this->resetValidation();
    }

    /**
     * Resets term form fields.
     * @return void
     */
    private function resetTermForm(): void
    {
        $this->reset(['termName', 'termDescription', 'termParentId', 'termOrder', 'editingTermId']);
        $this->isCreatingTerm = false;
        $this->isEditingTerm = false;
        $this->resetValidation();
    }

    /**
     * Closes the modal and resets all related form states.
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetTaxonomyForm();
        $this->resetTermForm();
    }

    /**
     * Selects a taxonomy to manage its terms.
     * @param int $taxonomyId
     * @return void
     */
    public function selectTaxonomyToManageTerms(int $taxonomyId): void
    {
        $this->selectedTaxonomyId = $taxonomyId;
        $this->currentTaxonomy = Taxonomy::find($taxonomyId);
        $this->editingTermId = null;
        $this->isCreatingTerm = false;
        $this->isEditingTerm = false;
        $this->resetPage('termsPage');
    }


    /**
     * Render the component.
     * @return View
     */
    public function render(): View
    {
        $taxonomiesQuery = Taxonomy::query();
        if (!empty($this->search) && !$this->selectedTaxonomyId) {
            $taxonomiesQuery->where(fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%'));
        }
        $taxonomies = $taxonomiesQuery->withCount('terms')->latest()->paginate($this->perPage, ['*'], 'taxonomiesPage');

        $terms = collect();
        $availableParentTerms = collect();

        if ($this->selectedTaxonomyId && $this->currentTaxonomy) {
            $termsQuery = Term::where('taxonomy_id', $this->selectedTaxonomyId);
            if (!empty($this->search)) {
                $termsQuery->where(fn($q) => $q->where('name', 'like', '%' . $this->search . '%')->orWhere('slug', 'like', '%' . $this->search . '%'));
            }
            $terms = $termsQuery->with('parent')->ordered()->paginate($this->perPage, ['*'], 'termsPage');

            if ($this->currentTaxonomy->hierarchical) {
                $availableParentTermsQuery = Term::where('taxonomy_id', $this->selectedTaxonomyId)
                    ->whereNull('parent_id') // Often only top-level terms are parents
                    ->ordered();
                if ($this->editingTermId) {
                    $availableParentTermsQuery->where('id', '!=', $this->editingTermId)
                        ->where(function($query) { // Prevent selecting descendants as parent
                            $termBeingEdited = Term::find($this->editingTermId);
                            if ($termBeingEdited) {
                                $descendantIds = $termBeingEdited->descendants()->pluck('id')->toArray();
                                $query->whereNotIn('id', $descendantIds);
                            }
                        });
                }
                $availableParentTerms = $availableParentTermsQuery->get();
            }
        }

        return view('livewire.admin.taxonomy-management', [
            'taxonomies' => $taxonomies,
            'terms' => $terms,
            'availableParentTerms' => $availableParentTerms,
        ]);
    }
}
