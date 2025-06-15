<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Settings') }}</flux:heading>
        <div class="flex items-center gap-2">
            <flux:icon name="globe-alt" class="w-5 h-5 text-zinc-400" />
            <flux:select wire:model.live="currentLocale" class="w-40">
                @foreach($locales as $localeCode => $localeName)
                    <option value="{{ $localeCode }}">{{ $localeName }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <div class="flex flex-col md:flex-row gap-8">
        <aside class="md:w-1/4" aria-label="{{ __('Settings navigation') }}">
            <div class="p-2 md:p-0">
                <flux:navlist>
                    @foreach($groups as $group)
                        <flux:navlist.item
                            href="#"
                            wire:click.prevent="selectTab('{{ $group->name }}')"
                            :current="$tab === '{{ $group->name }}'"
                            icon="{{ $group->icon }}"
                        >
                            {{ $group->getTranslation('name', $currentLocale) ?? $group->name }}
                        </flux:navlist.item>
                    @endforeach
                </flux:navlist>
            </div>
        </aside>

        <div class="flex-1">
            @foreach($groups as $group)
                @if($tab === $group->name)
                    <div wire:key="group-{{ $group->id }}">
                        <div class="space-y-6">
                            <flux:heading size="lg">{{ $group->getTranslation('name', $currentLocale) ?? $group->name }}</flux:heading>
                            <flux:text class="text-sm text-gray-600">
                                {{ $group->getTranslation('description', $currentLocale) ?? $group->description }}
                            </flux:text>

                            <div class="space-y-6 pt-6">
                                @foreach($group->settings as $setting)
                                    <div wire:key="setting-{{ $setting->key }}">
                                        @include('livewire.admin.settings.setting-field')
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                @endif
            @endforeach
            <div class="flex justify-end mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                <flux:button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    :disabled="!auth()->user()->can('edit-settings')"
                    variant="primary"
                >
                    {{ __('Save Changes') }}
                </flux:button>
            </div>
        </div>
    </div>
</div>
