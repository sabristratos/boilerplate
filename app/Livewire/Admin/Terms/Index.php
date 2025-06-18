<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Terms;

use App\Livewire\Traits\WithFiltering;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Services\TermService;
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
    use WithFiltering;

    public Taxonomy $taxonomy;
    public bool $confirmingDelete = false;
    public ?Term $deletingTerm = null;

    protected array $searchableColumns = ['name', 'description'];

    public function mount(Taxonomy $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
    }

    #[On('term-saved')]
    public function refresh(): void
    {
        // This will refresh the component rendering the term list.
    }

    #[On('confirm-delete-term')]
    public function confirmDeleteTerm(Term $term): void
    {
        Gate::authorize('delete-terms');
        $this->deletingTerm = $term;
        $this->confirmingDelete = true;
    }

    public function delete(TermService $termService): void
    {
        Gate::authorize('delete-terms');
        if (!$this->deletingTerm instanceof \App\Models\Term) {
            return;
        }

        try {
            $termService->delete($this->deletingTerm);
            Flux::toast(
                text: __('Term deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
            $this->dispatch('term-saved');
        } catch (\Exception $e) {
            Log::error('Failed to delete term: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete term. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }

        $this->confirmingDelete = false;
        $this->deletingTerm = null;
    }

    public function render(): View
    {
        $query = $this->taxonomy->terms()
            ->withCount('children');

        $query = $this->applySearching($query, $this->searchableColumns);
        $terms = $this->applySorting($query)->paginate($this->perPage);

        return view('livewire.admin.terms.index', [
            'terms' => $terms,
        ]);
    }
} 