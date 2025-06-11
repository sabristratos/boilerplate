<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">
            {{ $user?->exists ? __('Edit User') : __('Create User') }}
        </flux:heading>
        <flux:button :href="route('admin.users.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Users') }}">
            {{ __('Back to Users') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:input wire:model="name" label="{{ __('Name') }}" required :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" />
            <flux:input wire:model="email" type="email" label="{{ __('Email') }}" required :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" />
            <flux:select 
                wire:model="status" 
                label="{{ __('Status') }}" 
                description="{{ __('The user\'s status determines their ability to log in.') }}" 
                required
                :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')"
            >
                @foreach($statuses as $status)
                    <option value="{{ $status->value }}">{{ str($status->name)->title() }}</option>
                @endforeach
            </flux:select>
    
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="password" type="password" label="{{ __('Password') }}" autocomplete="new-password" :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" />
                <flux:input wire:model="password_confirmation" type="password" label="{{ __('Confirm Password') }}" autocomplete="new-password" :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" />
            </div>
    
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $user?->exists ? __('Leave password fields blank to keep the current password.') : __('Set a password for the new user.') }}
            </p>
    
            <flux:checkbox.group label="{{ __('Roles') }}" wire:model="selectedRoles">
                <div class="grid grid-cols-2 gap-4">
                    @foreach($roles as $role)
                        <flux:checkbox value="{{ $role->id }}" label="{{ $role->name }}" :disabled="!auth()->user()->can('assign-roles')" />
                    @endforeach
                </div>
            </flux:checkbox.group>
        </div>
    
        <div class="flex justify-end space-x-3 mt-8">
            <flux:button type="button" variant="outline" :href="route('admin.users.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')">
                <span wire:loading.remove wire:target="save">
                    {{ $user?->exists ? __('Save Changes') : __('Create User') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $user?->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 