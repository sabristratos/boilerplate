<?php

namespace App\Livewire\Admin\Crud;

use App\Crud\CrudConfigInterface;
use App\Events\Crud\EntityDeleted;
use App\Livewire\Traits\WithFiltering;
use App\Services\ImpersonationService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use App\Models\User;

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
    public array $actions = [];
    public array $rowActions = [];
    public array $globalActions = [];

    public function mount(string $alias, string $crud_config_class): void
    {
        $this->alias = $alias;
        $this->configClass = $crud_config_class;
        $this->initConfig();
        $this->authorize($this->config->getPermissionPrefix() . '.viewAny');

        $this->sortBy = $this->config->getDefaultSortField();
        $this->sortDirection = $this->config->getDefaultSortDirection();

        $this->actions = $this->config->getActions();
        $this->rowActions = collect($this->actions)->where('type', 'row_action')->all();
        $this->globalActions = collect($this->actions)->where('type', 'global_action')->all();
    }

    #[On('confirm-delete')]
    public function askToDelete(int $id): void
    {
        $this->initConfig();
        $this->authorize($this->config->getPermissionPrefix() . '.delete');
        $this->confirmingDeleteId = $id;
        Flux::modal('confirm-delete-modal')->show();
    }

    public function handleAction(string $method, ?int $id = null): void
    {
        $this->initConfig();
        $action = collect($this->actions)->firstWhere('method', $method);

        if (!$action) {
            return;
        }

        if (isset($action['permission'])) {
            $this->authorize($action['permission']);
        }

        if ($id) {
            $model = $this->config->getModelClass()::findOrFail($id);
            $this->{$method}($model);
        } else {
            $this->{$method}();
        }
    }

    public function impersonateUser(User $user, ImpersonationService $impersonationService)
    {
        if ($impersonationService->impersonate(auth()->user(), $user)) {
            return $this->redirect(route('home'));
        }

        Flux::toast(
            heading: __('Impersonation Failed'),
            text: __('Could not impersonate user. You may already be impersonating someone.'),
            variant: 'danger'
        );
    }

    public function copyLink(\App\Models\LegalPage $legalPage)
    {
        $url = route('legal.show', [
            'slug' => $legalPage->getTranslation('slug', $legalPage->first_available_locale)
        ]);

        $this->dispatch('copy-to-clipboard', text: $url);

        Flux::toast(
            heading: __('Link Copied'),
            text: __('The page link has been copied to your clipboard.'),
            variant: 'success'
        );
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

            event(new EntityDeleted($model, auth()->user()));

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
                $filterConfig = $this->config->getFilters()[$field] ?? [];
                $filterType = $filterConfig['type'] ?? 'select';

                if ($filterConfig['relationship'] ?? false) {
                    $query->whereHas($field, fn (Builder $q) => $q->where('id', $value));
                    continue;
                }

                match ($filterType) {
                    'boolean', 'select' => $query->where($field, $value),
                    'date' => $query->whereDate($field, $value),
                    default => $query->where($field, $value),
                };
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