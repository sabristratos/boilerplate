<?php

declare(strict_types=1);

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Builder;

trait WithFiltering
{
    public string $search = '';
    public int $perPage = 10;
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
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

    public function applySorting(Builder $query): Builder
    {
        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    public function applySearching(Builder $query, array $searchableColumns): Builder
    {
        return $query->when($this->search, function ($query) use ($searchableColumns) {
            $query->where(function ($query) use ($searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $query->orWhere($column, 'like', '%' . $this->search . '%');
                }
            });
        });
    }

    public function hasFilters(): bool
    {
        return ! empty($this->search);
    }
} 