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
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:input 
                    wire:model="name" 
                    required 
                    :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" 
                />
                <flux:error name="name" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input 
                    wire:model="email" 
                    type="email" 
                    required 
                    :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" 
                />
                <flux:error name="email" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Status') }}</flux:label>
                <flux:description>{{ __('The user\'s status determines their ability to log in.') }}</flux:description>
                <flux:select 
                    wire:model="status" 
                    required
                    :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')"
                >
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}">{{ str($status->name)->title() }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="status" />
            </flux:field>
    
            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>{{ __('Password') }}</flux:label>
                    <flux:input 
                        wire:model="password" 
                        type="password" 
                        autocomplete="new-password" 
                        :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" 
                    />
                    <flux:error name="password" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Confirm Password') }}</flux:label>
                    <flux:input 
                        wire:model="password_confirmation" 
                        type="password" 
                        autocomplete="new-password" 
                        :disabled="!auth()->user()->can($user?->exists ? 'edit-users' : 'create-users')" 
                    />
                    <flux:error name="password_confirmation" />
                </flux:field>
            </div>
    
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $user?->exists ? __('Leave password fields blank to keep the current password.') : __('Set a password for the new user.') }}
            </p>
    
            <flux:field>
                <flux:label>{{ __('Roles') }}</flux:label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($roles as $role)
                        <flux:checkbox 
                            wire:model="selectedRoles" 
                            value="{{ $role->id }}" 
                            label="{{ $role->name }}" 
                            :disabled="!auth()->user()->can('assign-roles')" 
                        />
                    @endforeach
                </div>
                <flux:error name="selectedRoles" />
            </flux:field>
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