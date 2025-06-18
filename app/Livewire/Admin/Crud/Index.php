<?php

namespace App\Livewire\Admin\Crud;

use App\Crud\CrudConfigInterface;
use App\Facades\ActivityLogger;
use App\Livewire\Traits\WithFiltering;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Index extends Component
{
    use WithPagination;
    use AuthorizesRequests;
    use WithFiltering;

    public string $alias;
    public string $configClass;
    private CrudConfigInterface $config;

    public ?int $confirmingDeleteId = null;
    public array $filters = [];

    public function mount(string $alias, string $crud_config_class): void
    {
        $this->alias = $alias;
        $this->configClass = $crud_config_class;
        $this->initConfig();
        $this->authorize($this->config->getPermissionPrefix() . '.viewAny');

        $this->sortBy = $this->config->getDefaultSortField();
        $this->sortDirection = $this->config->getDefaultSortDirection();
    }

    #[On('confirm-delete')]
    public function askToDelete(int $id): void
    {
        $this->authorize($this->config->getPermissionPrefix() . '.delete');
        $this->confirmingDeleteId = $id;
        Flux::modal('confirm-delete-modal')->show();
    }

    private function initConfig(): void
    {
        if (empty($this->config)) {
            $this->config = new $this->configClass();
        }
    }

    public function delete(): void
    {
        $this->initConfig();
        $this->authorize($this->config->getPermissionPrefix() . '.delete');

        if ($this->confirmingDeleteId) {
            $modelClass = $this->config->getModelClass();
            $model = $modelClass::findOrFail($this->confirmingDeleteId);
            $entityName = $this->config->getEntityName();

            $model->delete();

            ActivityLogger::logDeleted($model);

            Flux::toast(
                heading: __(':entity_name deleted', ['entity_name' => $entityName]),
                text: __('The :entity_name has been deleted successfully.', ['entity_name' => strtolower($entityName)]),
                variant: 'success'
            );
        }

        $this->confirmingDeleteId = null;
        Flux::modal('confirm-delete-modal')->close();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'filters']);
        $this->resetPage();
    }

    public function render(): View
    {
        $this->initConfig();
        $modelClass = $this->config->getModelClass();
        /** @var Builder $query */
        $query = $modelClass::query();

        if ($this->search) {
            $query->where(function (Builder $query) {
                foreach ($this->config->getSearchableFields() as $field) {
                    $query->orWhere($field, 'like', '%' . $this->search . '%');
                }
            });
        }

        foreach ($this->filters as $field => $value) {
            if ($value) {
                $query->where($field, $value);
            }
        }

        if (count($this->config->getEagerLoadRelations()) > 0) {
            $query->with($this->config->getEagerLoadRelations());
        }

        if ($this->sortBy) {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $items = $query->paginate($this->perPage);

        return view('livewire.admin.crud.index', [
            'items' => $items,
            'config' => $this->config,
        ])->layout('components.layouts.admin');
    }
} 