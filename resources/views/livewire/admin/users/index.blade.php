<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('User Management') }}</flux:heading>
        @can('create-users')
        <flux:button variant="primary" icon="plus" :href="route('admin.users.create')">{{ __('Create User') }}</flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="my-8" />

    @if ($users->total() === 0 && !$this->hasFilters())
        <x-empty-state
            icon="user-group"
            heading="{{ __('No users yet') }}"
            description="{{ __('Get started by creating your first user.') }}">
            @can('create-users')
            <flux:button variant="primary" icon="plus" :href="route('admin.users.create')">{{ __('Create User') }}</flux:button>
            @endcan
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="status">
                <option value="">{{ __('All Statuses') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ str($status->name)->title() }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="role">
                <option value="">{{ __('All Roles') }}</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </flux:select>
        </div>
        @if($users->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No users found') }}"
                description="{{ __('Try adjusting your search or filter to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$users">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column>{{ __('Roles') }}</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'last_activity'" :direction="$sortDirection" wire:click="sort('last_activity')">{{ __('Last Activity') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($users as $user)
                        <flux:table.row :key="$user->id">
                            <flux:table.cell>
                                <div class="flex items-center">
                                    <flux:avatar src="{{ $user->getAvatarUrl() }}" alt="{{ $user->name }}" />
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :variant="$user->status->color()">{{ $user->status->name }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @foreach($user->roles as $role)
                                    <flux:badge variant="info">{{ $role->name }}</flux:badge>
                                @endforeach
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $user->last_activity ? \Carbon\Carbon::createFromTimestamp($user->last_activity)->diffForHumans() : __('Never') }}
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical" variant="ghost" tooltip="{{ __('Actions') }}" />
                                    <flux:menu>
                                        <flux:menu.item :href="route('admin.users.edit', $user)" icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                        @if (auth()->id() !== $user->id)
                                            <flux:menu.item :href="route('admin.users.impersonate', $user)" icon="user-circle">{{ __('Impersonate') }}</flux:menu.item>
                                        @endif
                                        <flux:menu.item wire:click="$dispatch('confirm-delete-user', { user: {{ $user->id }} })" icon="trash">{{ __('Delete') }}</flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="confirmingDelete" class="md:w-96">
        @if ($deletingUser)
            <flux:heading size="lg">{{ __('Delete User: ') . $deletingUser->name }}</flux:heading>
            <p class="mt-2">{{ __('Are you sure you want to delete this user? This action cannot be undone.') }}</p>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button variant="outline" wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        @endif
    </flux:modal>
</div> 