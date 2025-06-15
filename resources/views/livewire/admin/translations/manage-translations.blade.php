<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Translations') }}</flux:heading>
        <div class="flex items-center space-x-2">
            <flux:button wire:click="export" icon="arrow-down-tray">{{ __('Export') }}</flux:button>
        </div>
    </div>

    <flux:card class="mb-4">
        <form wire:submit.prevent="import" class="flex items-center space-x-4">
            <div class="shrink-0">
                <flux:input type="file" wire:model="file" id="file-upload" class="hidden" />
                <label for="file-upload">
                    <flux:button type="button" as="span" icon="arrow-up-tray">{{ __('Import File') }}</flux:button>
                </label>
            </div>
            <div class="flex-1">
                @if ($file)
                    <p class="text-sm text-gray-500">{{ $file->getClientOriginalName() }}</p>
                @else
                    <p class="text-sm text-gray-500">{{ __('Select a CSV or XLSX file to import.') }}</p>
                @endif
            </div>
            <div class="shrink-0">
                <flux:button type="submit" variant="primary" :disabled="!$file">{{ __('Import') }}</flux:button>
            </div>
        </form>
        @error('file') <span class="text-red-500 text-sm mt-2">{{ $message }}</span> @enderror
    </flux:card>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
        <div class="lg:col-span-2">
            <flux:select wire:model.live="selectedLocales" variant="listbox" multiple placeholder="{{ __('Select locales...') }}">
                @foreach($allLocales as $locale)
                    <flux:select.option value="{{ $locale }}">{{ strtoupper($locale) }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <flux:select wire:model.live="perPage">
            <flux:select.option value="10">10</flux:select.option>
            <flux:select.option value="25">25</flux:select.option>
            <flux:select.option value="50">50</flux:select.option>
        </flux:select>
    </div>

    <form wire:submit="save">
        <div class="flex justify-end mb-4">
            <flux:button type="submit" variant="primary">{{ __('Save changes') }}</flux:button>
        </div>

        @if($keys->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No translations found') }}"
                description="{{ __('Try adjusting your search or filters to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$keys">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'key'" :direction="$sortDirection" wire:click="sort('key')">{{ __(strtoupper($defaultLocale)) }}</flux:table.column>
                    @foreach($selectedLocales as $locale)
                        @if($locale !== $defaultLocale)
                            <flux:table.column>{{ strtoupper($locale) }}</flux:table.column>
                        @endif
                    @endforeach
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($keys as $key)
                        <flux:table.row :key="$key->key">
                            <flux:table.cell class="align-top min-w-64 max-w-lg">
                                <flux:input wire:model.defer="translationsData.{{ $key->key }}.{{ $defaultLocale }}" />
                                <div class="text-xs text-zinc-400 mt-1">{{ $key->key }}</div>
                            </flux:table.cell>
                            @foreach($selectedLocales as $locale)
                                @if($locale !== $defaultLocale)
                                    <flux:table.cell class="align-top min-w-64 max-w-lg">
                                        <flux:input wire:model.defer="translationsData.{{ $key->key }}.{{ $locale }}" />
                                    </flux:table.cell>
                                @endif
                            @endforeach
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif
    </form>
</div>
