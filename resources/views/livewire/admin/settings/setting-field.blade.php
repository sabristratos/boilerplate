<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="col-span-1">
        <label for="values.{{ $setting->key }}" class="block text-sm font-medium text-gray-900 dark:text-white">{{ $setting->getTranslation('display_name', $currentLocale) ?? $setting->display_name }}</label>
        @if($setting->getTranslation('description', $currentLocale))
            <p class="text-sm text-gray-500 mt-1">{{ $setting->getTranslation('description', $currentLocale) ?? $setting->description }}</p>
        @endif
    </div>
    <div class="col-span-2">
        @switch($setting->type)
            @case(\App\Enums\SettingType::TEXT)
                <flux:input
                    wire:key="setting-{{ $setting->key }}-{{ $currentLocale }}"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::TEXTAREA)
                <flux:textarea
                    wire:key="setting-{{ $setting->key }}-{{ $currentLocale }}"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::SELECT)
                <flux:select
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                >
                    @foreach($setting->getTranslatedOptionsAttribute() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </flux:select>
                @break
            @case(\App\Enums\SettingType::MULTISELECT)
                <flux:select
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                    variant="listbox"
                    searchable
                    multiple
                >
                    @foreach($setting->getTranslatedOptionsAttribute() as $value => $label)
                        <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                    @endforeach
                </flux:select>
                @break
            @case(\App\Enums\SettingType::CHECKBOX)
            @case(\App\Enums\SettingType::BOOLEAN)
                <flux:switch
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::NUMBER)
                <flux:input
                    type="number"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::EMAIL)
                <flux:input
                    type="email"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::URL)
                <flux:input
                    type="url"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::PASSWORD)
                <flux:input
                    type="password"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::DATE)
                <flux:input
                    type="date"
                    wire:model.defer="values.{{ $setting->key }}"
                    :disabled="!auth()->user()->can('edit-settings')"
                />
                @break
            @case(\App\Enums\SettingType::FILE)
                <livewire:attachments.upload-attachment
                    wire:key="setting-file-{{ $setting->key }}"
                    :model-id="$setting->id"
                    model-type="App\Models\SettingFile"
                    :collection-name="$setting->key"
                    :label="$setting->getTranslation('display_name', $currentLocale)"
                    :description="$setting->getTranslation('description', $currentLocale)"
                    :can-delete="auth()->user()->can('edit-settings')"
                    :rules="$setting->validation_rules"
                    wire:model="values.{{ $setting->key }}"
                />
                @break
        @endswitch
        <flux:error name="values.{{ $setting->key }}" class="mt-2" />
    </div>
</div> 