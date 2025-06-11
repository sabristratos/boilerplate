<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Role Management') }}</flux:heading>
        @can('create-roles')
        <flux:button variant="primary" icon="plus" :href="route('admin.roles.create')">{{ __('Create Role') }}</flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="my-8" />

    @if($roles->total() === 0 && !$this->hasFilters())
        <x-empty-state
            icon="shield-check"
            heading="{{ __('No roles yet') }}"
            description="{{ __('Get started by creating your first role.') }}">
            @can('create-roles')
            <flux:button variant="primary" icon="plus" :href="route('admin.roles.create')">{{ __('Create Role') }}</flux:button>
            @endcan
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </flux:select>
        </div>
        @if($roles->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No roles found') }}"
                description="{{ __('Try adjusting your search to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$roles">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Permissions') }}</flux:table.column>
                    <flux:table.column>{{ __('Users') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($roles as $role)
                        <flux:table.row :key="$role->id">
                            <flux:table.cell>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</div>
                                <div class="text-sm text-gray-500">{{ $role->description }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="info">{{ $role->permissions_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="secondary">{{ $role->users_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical" variant="ghost" tooltip="{{ __('Actions') }}" />
                                    <flux:menu>
                                        @can('edit-roles')
                                        <flux:menu.item :href="route('admin.roles.edit', $role)" icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                        @endcan
                                        @if(! $role->is_system)
                                            @can('delete-roles')
                                            <flux:menu.item wire:click="$dispatch('confirm-delete-role', { role: {{ $role->id }} })" icon="trash">{{ __('Delete') }}</flux:menu.item>
                                            @endcan
                                        @endif
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $roles->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="confirmingDelete" class="md:w-96">
        @if ($deletingRole)
            <flux:heading size="lg">{{ __('Delete Role: ') . $deletingRole->name }}</flux:heading>
            <p class="mt-2">{{ __('Are you sure you want to delete this role? This action cannot be undone.') }}</p>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button variant="outline" wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        @endif
    </flux:modal>
</div> 