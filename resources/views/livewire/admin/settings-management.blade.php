<div>
    <flux:heading size="xl">{{ __('Settings') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <flux:tab.group>
        <flux:tabs wire:model.live="tab">
            @foreach($groups as $group)
                <flux:tab name="{{ $group->slug }}">{{ $group->name }}</flux:tab>
            @endforeach
        </flux:tabs>

        @foreach($groups as $group)
            <flux:tab.panel name="{{ $group->slug }}">
                <div class="space-y-6 py-4">
                    <flux:heading size="lg">{{ $group->name }}</flux:heading>
                    <flux:text class="text-sm text-gray-600">
                        {{ $group->description }}
                    </flux:text>

                    <div class="space-y-4">
                        @foreach($group->settings as $setting)
                            <div>
                                @if($setting->type === \App\Enums\SettingType::TEXT)
                                    <flux:input
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        placeholder="{{ $setting->description }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::TEXTAREA)
                                    <flux:textarea
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        placeholder="{{ $setting->description }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::SELECT)
                                    <flux:select
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    >
                                        @foreach($setting->options as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </flux:select>
                                @elseif($setting->type === \App\Enums\SettingType::CHECKBOX)
                                    <flux:field>
                                        <flux:label></flux:label>
                                        <flux:switch
                                            name="values.{{ $setting->key }}"
                                           label="{{ $setting->display_name }}"
                                           wire:model="values.{{ $setting->key }}" align="left" />
                                    </flux:field>
                                @elseif($setting->type === \App\Enums\SettingType::COLOR)
                                    <flux:input
                                        type="color"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::NUMBER)
                                    <flux:input
                                        type="number"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::EMAIL)
                                    <flux:input
                                        type="email"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::URL)
                                    <flux:input
                                        type="url"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::PASSWORD)
                                    <flux:input
                                        type="password"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::DATE)
                                    <flux:input
                                        type="date"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::DATETIME)
                                    <flux:input
                                        type="datetime-local"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::TIME)
                                    <flux:input
                                        type="time"
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    />
                                @elseif($setting->type === \App\Enums\SettingType::RADIO)
                                    <flux:radio.group
                                        label="{{ $setting->display_name }}"
                                        wire:model="values.{{ $setting->key }}"
                                        :error="$errors->first('values.' . $setting->key) ?? null"
                                    >
                                        @foreach($setting->options as $value => $label)
                                            <flux:radio value="{{ $value }}">{{ $label }}</flux:radio>
                                        @endforeach
                                    </flux:radio.group>
                                @endif

                                @if($setting->description && !in_array($setting->type, [\App\Enums\SettingType::CHECKBOX]))
                                    <flux:text class="text-xs text-gray-500 mt-1">
                                        {{ $setting->description }}
                                    </flux:text>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </flux:tab.panel>
        @endforeach
    </flux:tab.group>

    <div class="flex justify-end mt-6">
        <flux:button variant="primary" wire:click="save">{{ __('Save Settings') }}</flux:button>
    </div>
</div>
