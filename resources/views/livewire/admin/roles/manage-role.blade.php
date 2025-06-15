<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button :href="route('admin.roles.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Roles') }}">
                {{ __('Back to Roles') }}
            </flux:button>
            <flux:heading size="xl">
                {{ $role?->exists ? __('Edit Role') : __('Create Role') }}
            </flux:heading>
        </div>
        <div class="flex items-center gap-2">
            <flux:icon name="globe-alt" class="text-zinc-400" />
            <flux:select
                wire:model.live="currentLocale"
                variant="listbox"
                size="sm"
                class="w-40"
            >
                @foreach($locales as $localeCode => $localeName)
                    <flux:select.option value="{{ $localeCode }}">
                        {{ $localeName }}
                    </flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit="save">
        <div class="max-w-3xl space-y-8">
            <div class="space-y-6">
                <flux:field>
                    <flux:label>{{ __('Name') }}</flux:label>
                    <flux:input
                        wire:key="role-name-{{ $currentLocale }}"
                        wire:model.defer="name.{{ $currentLocale }}"
                        :disabled="!auth()->user()->can($role?->exists ? 'update' : 'create', $role ?? App\Models\Role::class)"
                    />
                    <flux:error name="name.{{ $currentLocale }}" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Description') }}</flux:label>
                    <flux:textarea
                        wire:key="role-description-{{ $currentLocale }}"
                        wire:model.defer="description.{{ $currentLocale }}"
                        :disabled="!auth()->user()->can($role?->exists ? 'update' : 'create', $role ?? App\Models\Role::class)"
                    />
                    <flux:error name="description.{{ $currentLocale }}" />
                </flux:field>

                <flux:field>
                    <flux:label>{{ __('Permissions') }}</flux:label>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($permissions as $permission)
                            <flux:checkbox
                                wire:model="selectedPermissions"
                                value="{{ $permission->id }}"
                                label="{{ $permission->name }}"
                                :disabled="!auth()->user()->can('assign-permissions')"
                            />
                        @endforeach
                    </div>
                    <flux:error name="selectedPermissions" />
                </flux:field>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <flux:button
                    type="button"
                    variant="ghost"
                    wire:click="$dispatch('role-saved')"
                >
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button
                    type="submit"
                    :disabled="!auth()->user()->can($role?->exists ? 'update' : 'create', $role ?? App\Models\Role::class)"
                >
                    {{ $role?->exists ? __('Update Role') : __('Create Role') }}
                </flux:button>
            </div>
        </div>
    </form>
</div> 