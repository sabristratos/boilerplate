<div>
    <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="grid grid-cols-12 gap-8">
        <div class="col-span-3">
            <flux:navlist>
                @foreach($groups as $group)
                    <flux:navlist.item
                        href="#"
                        wire:click.prevent="$set('tab', '{{ $group->slug }}')"
                        :current="$tab === $group->slug"
                        :icon="$group->icon"
                    >
                        {{ $group->name }}
                    </flux:navlist.item>
                @endforeach
            </flux:navlist>
        </div>
        <div class="col-span-9">
            @foreach($groups as $group)
                @if($tab === $group->slug)
                    <div wire:key="group-{{ $group->slug }}">
                        <div class="space-y-6">
                            <flux:heading size="lg">{{ $group->name }}</flux:heading>
                            <flux:text class="text-sm text-gray-600">
                                {{ $group->description }}
                            </flux:text>

                            <div class="space-y-4">
                                @foreach($group->settings as $setting)
                                    <div>
                                        @if($setting->type === \App\Enums\SettingType::TEXT)
                                            <flux:input
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::TEXTAREA)
                                            <flux:textarea
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::SELECT)
                                            <flux:select
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            >
                                                @foreach($setting->translated_options as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </flux:select>
                                        @elseif($setting->type === \App\Enums\SettingType::MULTISELECT)
                                            <fieldset>
                                                <legend class="text-sm font-medium text-gray-900 dark:text-white">{{ $setting->display_name }}</legend>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $setting->description }}</p>
                                                <div class="mt-4 space-y-4">
                                                    @foreach($setting->translated_options as $value => $label)
                                                        <flux:checkbox
                                                            :label="$label"
                                                            :value="$value"
                                                            wire:model="values.{{ $setting->key }}.{{$value}}"
                                                            :disabled="!auth()->user()->can('edit-settings')"
                                                        />
                                                    @endforeach
                                                </div>
                                            </fieldset>
                                        @elseif($setting->type === \App\Enums\SettingType::CHECKBOX)
                                            <flux:switch
                                                name="values.{{ $setting->key }}"
                                                :label="$setting->display_name"
                                                wire:model="values.{{ $setting->key }}"
                                                align="left"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::COLOR)
                                            <flux:input
                                                type="color"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::NUMBER)
                                            <flux:input
                                                type="number"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::EMAIL)
                                            <flux:input
                                                type="email"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::URL)
                                            <flux:input
                                                type="url"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                                :disabled="!auth()->user()->can('edit-settings')"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::PASSWORD)
                                            <flux:input
                                                type="password"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::DATE)
                                            <flux:input
                                                type="date"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::DATETIME)
                                            <flux:input
                                                type="datetime-local"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::TIME)
                                            <flux:input
                                                type="time"
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                            />
                                        @elseif($setting->type === \App\Enums\SettingType::RADIO)
                                            <flux:radio.group
                                                :label="$setting->display_name"
                                                :description:trailing="$setting->description"
                                                wire:model="values.{{ $setting->key }}"
                                                :error="$errors->first('values.' . $setting->key) ?? null"
                                            >
                                                @foreach($setting->translated_options as $value => $label)
                                                    <flux:radio value="{{ $value }}">{{ $label }}</flux:radio>
                                                @endforeach
                                            </flux:radio.group>
                                        @elseif($setting->type === \App\Enums\SettingType::FILE)
                                            <div
                                                x-data="{
                                                    isUploading: false,
                                                    progress: 0,
                                                    isUploaded: {{ $values[$setting->key] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? 'true' : 'false' }},
                                                }"
                                                x-on:livewire-upload-start="isUploading = true"
                                                x-on:livewire-upload-finish="isUploading = false; isUploaded = true"
                                                x-on:livewire-upload-error="isUploading = false"
                                                x-on:livewire-upload-progress="progress = $event.detail.progress"
                                                class="flex items-center gap-4"
                                            >
                                                @if(isset($values[$setting->key]) && $values[$setting->key] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                                    <div class="relative">
                                                        <img
                                                            src="{{ $values[$setting->key]->temporaryUrl() }}"
                                                            class="w-24 h-24 rounded-md object-cover"
                                                        />
                                                        <div x-show="isUploading" class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                                <div class="bg-blue-600 h-1.5 rounded-full" :style="`width: ${progress}%`"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif(isset($values[$setting->key]) && is_numeric($values[$setting->key]))
                                                    @php($attachment = \App\Models\Attachment::find($values[$setting->key]))
                                                    @if($attachment)
                                                        <img src="{{ $attachment->url }}" class="w-24 h-24 rounded-md object-cover">
                                                    @endif
                                                @endif
                                                <flux:input
                                                    type="file"
                                                    :label="$setting->display_name"
                                                    wire:model="values.{{ $setting->key }}"
                                                    :description:trailing="$setting->description"
                                                    :error="$errors->first('values.' . $setting->key) ?? null"
                                                />
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    <div class="flex justify-end mt-6">
        <flux:button variant="primary" wire:click="save">{{ __('Save Settings') }}</flux:button>
    </div>
</div>
