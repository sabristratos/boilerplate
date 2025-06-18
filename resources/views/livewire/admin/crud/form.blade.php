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
        <div class="col-span-12 lg:col-span-8 space-y-6">
            @foreach($config->getFormFields() as $field)
                @php
                    $isTranslatable = $field['translatable'] ?? false;
                    $modelName = 'data.' . $field['name'];
                    if ($isTranslatable) {
                        $modelName .= '.' . $currentLocale;
                    }
                    $key = 'form-field.' . $field['name'] . ($isTranslatable ? '.' . $currentLocale : '');
                @endphp
                <flux:field>
                    <flux:label>{{ __($field['label']) }}</flux:label>
                    @if(isset($field['description']))
                        <flux:description>{{ __($field['description']) }}</flux:description>
                    @endif

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
                                @foreach($field['options'] as $value => $label)
                                    <option value="{{ $value }}">{{ __($label) }}</option>
                                @endforeach
                            </flux:select>
                            @break
                        @case('file')
                            <div class="flex items-center space-x-4">
                                @if (isset($this->data[$field['name']]) && !is_string($this->data[$field['name']]))
                                    <div class="w-16 h-16 rounded-lg overflow-hidden">
                                        <img src="{{ $this->data[$field['name']]->temporaryUrl() }}" class="w-full h-full object-cover">
                                    </div>
                                @elseif($model->attachments->where('collection', $field['name'])->first())
                                     <div class="w-16 h-16 rounded-lg overflow-hidden">
                                        <img src="{{ $model->attachments->where('collection', $field['name'])->first()->url }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <flux:input
                                    wire:key="{{ $key }}"
                                    wire:model.blur="{{ $modelName }}"
                                    type="file"
                                />
                            </div>
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
                    <flux:error :name="$modelName" />
                </flux:field>
            @endforeach
        </div>

        <div class="col-span-12 lg:col-span-4">
            <div class="sticky top-20">
                <flux:card>
                    <div class="p-6 flex flex-col gap-3">
                        <flux:button type="submit" class="w-full justify-center">
                           {{ __('Save') }}
                        </flux:button>
                        <flux:button wire:navigate :href="route('admin.crud.index', ['alias' => $alias])" variant="ghost" class="w-full justify-center">
                            {{ __('Cancel') }}
                        </flux:button>
                    </div>
                </flux:card>
            </div>
        </div>
    </form>
</div> 