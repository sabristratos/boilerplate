<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Taxonomies;

use App\Models\Taxonomy;
use App\Services\TaxonomyService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
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

    public string $search = '';
    public int $perPage = 10;
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public ?string $isHierarchical = null;

    public bool $confirmingDelete = false;
    public ?Taxonomy $deletingTaxonomy = null;

    public function hasFilters(): bool
    {
        return !empty($this->search) || ! is_null($this->isHierarchical);
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $column;
        $this->resetPage();
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
        if (! $this->deletingTaxonomy) {
            return;
        }

        try {
            $taxonomyService->deleteTaxonomy($this->deletingTaxonomy);
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
        $taxonomies = Taxonomy::query()
            ->with(['terms' => fn ($query) => $query->limit(3)])
            ->withCount('terms')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->isHierarchical, function ($query, $isHierarchical) {
                $query->where('hierarchical', $isHierarchical === 'yes');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.taxonomies.index', [
            'taxonomies' => $taxonomies,
        ]);
    }
} 