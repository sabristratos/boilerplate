<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">
            {{ $role?->exists ? __('Edit Role') : __('Create Role') }}
        </flux:heading>
        <flux:button :href="route('admin.roles.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Roles') }}">
            {{ __('Back to Roles') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:input wire:model="name" label="{{ __('Name') }}" required :disabled="!auth()->user()->can($role?->exists ? 'edit-roles' : 'create-roles')" />
            <flux:input 
                wire:model="description" 
                label="{{ __('Description') }}"
                description="{{ __('A short description of what this role is for.') }}"
                :disabled="!auth()->user()->can($role?->exists ? 'edit-roles' : 'create-roles')"
            />

            <div>
                <flux:label>{{ __('Permissions') }}</flux:label>
                <flux:text size="sm" class="mt-1">
                    {{ __('Select the permissions this role should have.') }}
                </flux:text>
                <div class="mt-4 space-y-4">
                    @foreach($permissions as $group => $permissionList)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ str($group)->headline() }}</h3>
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($permissionList as $permission)
                                    <flux:checkbox
                                        value="{{ $permission->id }}"
                                        label="{{ $permission->name }}"
                                        wire:model="selectedPermissions"
                                        :disabled="!auth()->user()->can($role?->exists ? 'edit-roles' : 'create-roles')"
                                    />
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    
        <div class="flex justify-end space-x-3 mt-8">
            <flux:button type="button" variant="outline" :href="route('admin.roles.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" :disabled="!auth()->user()->can($role?->exists ? 'edit-roles' : 'create-roles')">
                <span wire:loading.remove wire:target="save">
                    {{ $role?->exists ? __('Save Changes') : __('Create Role') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $role?->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 