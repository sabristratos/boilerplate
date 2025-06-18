<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button
                wire:navigate
                :href="route('admin.crud.index', ['alias' => $alias])"
                variant="outline"
                icon="arrow-left"
            >
                {{ __('Back to :name', ['name' => $config->getEntityPluralName()]) }}
            </flux:button>
            <flux:heading size="xl">
                {{ $model->exists ? __('Edit :name', ['name' => $config->getEntityName()]) : __('New :name', ['name' => $config->getEntityName()]) }}
            </flux:heading>
        </div>
        @if (count($availableLocales) > 1)
            <div class="flex items-center gap-2">
                <flux:icon name="globe-alt" class="w-5 h-5 text-zinc-400" />
                <flux:select wire:model.live="currentLocale" class="w-40">
                    @foreach($availableLocales as $localeCode => $localeName)
                        <option value="{{ $localeCode }}">{{ $localeName }}</option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit="save" class="grid grid-cols-12 gap-6">
        <div class="col-span-12 lg:col-span-8">
            <div class="grid grid-cols-2 gap-6">
                @foreach($config->getFormFields() as $field)
                    @php
                        $isTranslatable = $field['translatable'] ?? false;
                        $modelName = 'data.' . $field['name'];
                        if ($isTranslatable) {
                            $modelName .= '.' . $currentLocale;
                        }
                        $key = 'form-field.' . $field['name'] . ($isTranslatable ? '.' . $currentLocale : '');
                        $columnSpan = $field['column_span'] ?? 6;
                    @endphp
                    <div class="col-span-{{ $columnSpan }}">
                        <flux:field>
                            <flux:label>{{ __($field['label']) }}</flux:label>
                            @if(isset($field['description']))
                                <flux:description>{{ __($field['description']) }}</flux:description>
                            @endif

                            @if(isset($field['component']))
                                <x-dynamic-component
                                    :component="$field['component']"
                                    :attributes="new \Illuminate\View\ComponentAttributeBag(
                                        array_merge($field, ['wire:model.blur' => $modelName])
                                    )"
                                />
                            @else
                                @switch($field['type'])
                                    @case('text')
                                    @case('password')
                                    @case('email')
                                    @case('number')
                                        <flux:input
                                            wire:key="{{ $key }}"
                                            wire:model.blur="{{ $modelName }}"
                                            type="{{ $field['type'] }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                        @break
                                    @case('textarea')
                                        <flux:textarea
                                            wire:key="{{ $key }}"
                                            wire:model.blur="{{ $modelName }}"
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        />
                                        @break
                                    @case('checkbox')
                                        <flux:switch
                                            wire:key="{{ $key }}"
                                            wire:model.blur="{{ $modelName }}"
                                        />
                                        @break
                                    @case('select')
                                        <flux:select
                                            wire:key="{{ $key }}"
                                            wire:model.blur="{{ $modelName }}"
                                        >
                                            @if(isset($field['placeholder']))
                                                <option value="" disabled>{{ $field['placeholder'] }}</option>
                                            @endif
                                            @foreach($field['options'] as $value => $label)
                                                <option value="{{ $value }}">{{ __($label) }}</option>
                                            @endforeach
                                        </flux:select>
                                        @break
                                    @case('multiselect')
                                        <flux:select
                                            wire:key="{{ $key }}"
                                            wire:model.blur="{{ $modelName }}"
                                            variant="listbox"
                                            multiple
                                            searchable
                                            placeholder="{{ $field['placeholder'] ?? '' }}"
                                        >
                                            @foreach($field['options'] as $value => $label)
                                                <flux:select.option value="{{ $value }}">{{ __($label) }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                        @break
                                    @case('file_upload')
                                        <x-file-upload
                                            wire:model="data.{{ $field['name'] }}"
                                            :model="$model"
                                            :collection="$field['collection']"
                                            :multiple="$field['multiple'] ?? false"
                                            :label="$field['label']"
                                            variant="default"
                                        />
                                        @break
                                    @case('circular')
                                        <x-file-upload
                                            wire:model="data.{{ $field['name'] }}"
                                            :model="$model"
                                            :collection="$field['collection']"
                                            :multiple="false"
                                            :label="$field['label']"
                                            variant="circular"
                                        />
                                        @break
                                    @case('editor')
                                        <div wire:ignore>
                                            <flux:editor
                                                wire:key="{{ $key }}"
                                                wire:model.defer="{{ $modelName }}"
                                            />
                                        </div>
                                        @break
                                @endswitch
                            @endif
                            <flux:error :name="$modelName" />
                        </flux:field>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="sticky top-20 space-y-6">
                @if($model->exists)
                    <flux:card>
                        <div>
                            <h3 class="font-medium text-lg">{{ __('Details') }}</h3>
                            <div class="mt-4 space-y-2 text-sm text-zinc-500">
                                <p><strong>{{ __('Created At') }}:</strong> {{ $model->created_at?->toFormattedDateString() }}</p>
                                <p><strong>{{ __('Last Updated') }}:</strong> {{ $model->updated_at?->toFormattedDateString() }}</p>
                            </div>
                        </div>
                    </flux:card>
                @endif
                <div class="flex flex-col gap-3">
                    <flux:button type="submit" variant="primary" class="w-full justify-center">
                        {{ __('Save') }}
                    </flux:button>
                    <flux:button wire:navigate :href="route('admin.crud.index', ['alias' => $alias])" variant="ghost" class="w-full justify-center">
                        {{ __('Cancel') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div> 