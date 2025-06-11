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
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class Index extends Component
{
    use WithPagination;

    public Taxonomy $taxonomy;
    public string $search = '';
    public int $perPage = 10;
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    public bool $confirmingDelete = false;
    public ?Term $deletingTerm = null;

    public function hasFilters(): bool
    {
        return !empty($this->search);
    }

    public function mount(Taxonomy $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
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
        if (! $this->deletingTerm) {
            return;
        }

        try {
            $termService->deleteTerm($this->deletingTerm);
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
        $terms = $this->taxonomy->terms()
            ->withCount('children')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.terms.index', [
            'terms' => $terms,
        ]);
    }
} 