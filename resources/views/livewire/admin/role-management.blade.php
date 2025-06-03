<div>
    <flux:heading size="xl">{{ __('Role Management') }}</flux:heading>

    <div class="flex justify-between items-center my-6">
        <div class="flex items-center space-x-2 w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search roles...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="perPage" class="w-24">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </flux:select>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Create Role') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Slug') }}</flux:table.column>
            <flux:table.column>{{ __('Description') }}</flux:table.column>
            <flux:table.column>{{ __('Users') }}</flux:table.column>
            <flux:table.column>{{ __('Permissions') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($roles as $role)
                <flux:table.row wire:key="role-{{ $role->id }}">
                    <flux:table.cell>{{ $role->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $role->slug }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-wrap max-w-72">{{ $role->description }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="blue" size="sm">{{ $role->users_count }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="purple" size="sm">{{ $role->permissions_count }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell align="end" class="space-x-2">
                            <flux:button wire:click="edit({{ $role->id }})" size="sm" icon="pencil" />
                            <flux:button wire:click="confirmDelete({{ $role->id }})" variant="danger" size="sm" icon="trash" />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center py-4">
                        <x-flux.empty-state
                            icon="shield-exclamation"
                            heading="{{ __('No roles found') }}"
                            description="{{ $search ? __('Try a different search term.') : __('Create your first role to get started.') }}"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($roles->hasPages())
        <div class="mt-6">
            <flux:pagination :paginator="$roles" />
        </div>
    @endif

    <flux:modal wire:model.self="showModal" class="md:w-[32rem]">
        @if($isCreating || $isEditing)
            <flux:heading size="lg">{{ $isCreating ? __('Create Role') : __('Edit Role') }}</flux:heading>

            <form wire:submit.prevent="{{ $isCreating ? 'store' : 'update' }}" class="space-y-6 mt-6">
                <flux:input
                    wire:model="name"
                    label="{{ __('Name') }}"
                    placeholder="{{ __('Enter role name') }}"
                    :error="$errors->first('name')"
                    required
                />

                <flux:input
                    wire:model="slug"
                    label="{{ __('Slug') }}"
                    placeholder="{{ __('role-slug') }}"
                    :error="$errors->first('slug')"
                    description="{{ __('Auto-generated from name if left empty, or customize it.') }}"
                />

                <flux:textarea
                    wire:model="description"
                    label="{{ __('Description') }}"
                    placeholder="{{ __('Briefly describe this role') }}"
                    :error="$errors->first('description')"
                    rows="3"
                />

                <div>
                    <flux:label class="mb-2">{{ __('Permissions') }}</flux:label>
                    <div class="max-h-60 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-2 p-1 border rounded-md dark:border-zinc-700">
                        @foreach ($permissions as $permission)
                            <flux:field variant="inline" wire:key="permission-{{ $permission->id }}">
                                <flux:checkbox
                                    wire:model="selectedPermissions"
                                    value="{{ (string)$permission->id }}"
                                    label="{{ $permission->name }}"
                                />
                            </flux:field>
                        @endforeach
                    </div>
                    @error('selectedPermissions') <flux:error :message="$message" /> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <flux:button type="button" variant="outline" wire:click="closeModal" class="dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $isCreating ? __('Create Role') : __('Save Changes') }}
                    </flux:button>
                </div>
            </form>

        @elseif($confirmingDelete)
            <flux:heading size="lg">{{ __('Delete Role') }}</flux:heading>
            <flux:text class="mt-4">
                {{ __('Are you sure you want to delete the role') }} "<strong>{{ $name }}</strong>"?
                {{ __('This action cannot be undone and will remove the role from all assigned users.') }}
            </flux:text>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button type="button" variant="outline" wire:click="closeModal" class="dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="delete" variant="danger">
                    {{ __('Delete Role') }}
                </flux:button>
            </div>
        @endif
    </flux:modal>
</div>
