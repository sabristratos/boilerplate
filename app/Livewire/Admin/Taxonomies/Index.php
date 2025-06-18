<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Taxonomies;

use App\Livewire\Traits\WithFiltering;
use App\Models\Taxonomy;
use App\Services\TaxonomyService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;
    use WithFiltering;

    public ?string $isHierarchical = null;

    public bool $confirmingDelete = false;
    public ?Taxonomy $deletingTaxonomy = null;

    protected array $searchableColumns = ['name', 'description'];

    public function hasFilters(): bool
    {
        return $this->search !== '' && $this->search !== '0' || ! is_null($this->isHierarchical);
    }

    #[On('taxonomy-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the taxonomy list.
    }

    #[On('confirm-delete-taxonomy')]
    public function confirmDeleteTaxonomy(Taxonomy $taxonomy): void
    {
        Gate::authorize('delete-taxonomies');
        $this->deletingTaxonomy = $taxonomy;
        $this->confirmingDelete = true;
    }

    public function delete(TaxonomyService $taxonomyService): void
    {
        Gate::authorize('delete-taxonomies');
        if (!$this->deletingTaxonomy instanceof \App\Models\Taxonomy) {
            return;
        }

        try {
            $taxonomyService->delete($this->deletingTaxonomy);
            Flux::toast(
                text: __('Taxonomy deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('taxonomy-saved');
        } catch (\Exception $e) {
            Log::error('Failed to delete taxonomy: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete taxonomy. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }

        $this->confirmingDelete = false;
        $this->deletingTaxonomy = null;
    }

    public function render(): View
    {
        $query = Taxonomy::query()
            ->with(['terms' => fn ($query) => $query->limit(3)])
            ->withCount('terms');

        $query = $this->applySearching($query, $this->searchableColumns);

        $query->when($this->isHierarchical, function (Builder $query, $isHierarchical) {
            $query->where('hierarchical', $isHierarchical === 'yes');
        });

        $taxonomies = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.taxonomies.index', [
            'taxonomies' => $taxonomies,
        ]);
    }
} 