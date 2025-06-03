<div>
    <flux:heading size="xl">{{ __('User Management') }}</flux:heading>

    <div class="flex justify-between items-center my-6">
        <div class="flex items-center space-x-2 w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search users...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="perPage" class="w-24">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </flux:select>
        </div>

        <flux:button wire:click="create" variant="primary" icon="plus">{{ __('Create User') }}</flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>{{ __('Name') }}</flux:table.column>
            <flux:table.column>{{ __('Email') }}</flux:table.column>
            <flux:table.column>{{ __('Roles') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($users as $user)
                <flux:table.row wire:key="{{ $user->id }}">
                    <flux:table.cell>
                        <div class="flex items-center space-x-3">
                            <flux:avatar src="{{ $user->avatar_url ?? '' }}" name="{{ $user->name }}" size="xs" />
                            <span>{{ $user->name }}</span>
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>{{ $user->email }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($user->roles as $role)
                                <flux:badge color="indigo" size="sm">{{ $role->name }}</flux:badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    <flux:table.cell align="end">
                        <div class="flex space-x-2 justify-end">
                            <flux:button wire:click="edit({{ $user->id }})" size="sm" icon="pencil"></flux:button>
                            <flux:button wire:click="confirmDelete({{ $user->id }})" variant="danger" size="sm" icon="trash"></flux:button>
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="4">
                        <x-flux.empty-state
                            icon="user-group"
                            heading="{{ __('No users found') }}"
                            description="{{ $search ? __('Try a different search term.') : __('Create your first user to get started.') }}"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    @if ($users->hasPages())
        <div class="mt-6">
            <flux:pagination :paginator="$users" />
        </div>
    @endif

    <flux:modal wire:model.self="showModal" class="md:w-[32rem]">
        @if($isCreating || $isEditing)
            <flux:heading size="lg">{{ $isCreating ? __('Create User') : __('Edit User') }}</flux:heading>

            <form wire:submit.prevent="{{ $isCreating ? 'store' : 'update' }}" class="space-y-6 mt-6">
                <flux:input
                    wire:model="name"
                    label="{{ __('Name') }}"
                    placeholder="{{ __('Enter full name') }}"
                    :error="$errors->first('name')"
                    required
                />

                <flux:input
                    wire:model="email"
                    label="{{ __('Email') }}"
                    type="email"
                    placeholder="{{ __('user@example.com') }}"
                    :error="$errors->first('email')"
                    required
                />

                <flux:input
                    wire:model="password"
                    label="{{ __('Password') }}"
                    type="password"
                    placeholder="{{ $isCreating ? __('Enter password') : __('Leave blank to keep current password') }}"
                    :error="$errors->first('password')"
                    description="{{ $isCreating ? __('Min. 8 characters.') : ''}}"
                />

                <flux:input
                    wire:model="password_confirmation"
                    label="{{ __('Confirm Password') }}"
                    type="password"
                    placeholder="{{ __('Confirm new password') }}"
                    :error="$errors->first('password_confirmation')"
                />

                <div>
                    <flux:label class="mb-2">{{ __('Assign Roles') }}</flux:label>
                    <div class="max-h-60 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-x-4 gap-y-2 p-1">
                        @foreach ($roles as $role)
                            <flux:field variant="inline" wire:key="role-{{ $role->id }}">
                                <flux:checkbox
                                    wire:model="selectedRoles"
                                    value="{{ (string)$role->id }}"
                                    label="{{ $role->name }}"
                                />
                            </flux:field>
                        @endforeach
                    </div>
                    @error('selectedRoles') <flux:error :message="$message" /> @enderror
                </div>

                <div class="flex justify-end space-x-3 mt-8">
                    <flux:button type="button" variant="outline" wire:click="closeModal" class="dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $isCreating ? __('Create User') : __('Save Changes') }}
                    </flux:button>
                </div>
            </form>

        @elseif($confirmingDelete)
            <flux:heading size="lg">{{ __('Delete User') }}</flux:heading>
            <flux:text class="mt-4">
                {{ __('Are you sure you want to delete the user') }} "<strong>{{ $name }}</strong>"?
                {{ __('This action cannot be undone.') }}
            </flux:text>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button type="button" variant="outline" wire:click="closeModal" class="dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button wire:click="delete" variant="danger">
                    {{ __('Delete User') }}
                </flux:button>
            </div>
        @endif
    </flux:modal>
</div>
