<div>
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4 mb-8">
        <div>
            <flux:heading size="xl">{{ __('Translations') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Manage all application translations.') }}</flux:text>
        </div>
        <div class="flex items-center gap-2">
            <flux:button wire:click="save" variant="primary">
                {{ __('Save') }}
            </flux:button>
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">
                    {{ __('Actions') }}
                </flux:button>
                <flux:menu>
                    <flux:menu.item wire:click="importFromFiles">{{ __('Import from Files') }}</flux:menu.item>
                    <flux:menu.item wire:click="exportToFiles">{{ __('Export to Files') }}</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click="export">{{ __('Export to Excel/CSV') }}</flux:menu.item>
                    <flux:menu.item x-on:click.prevent="$flux.modal('import-modal').show()">{{ __('Import from Excel/CSV') }}</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </div>
    </div>

    <div class="space-y-4">
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

        <flux:table class="table-fixed">
            <flux:table.columns>
                <flux:table.column class="w-64">{{ __('Key') }}</flux:table.column>
                @foreach($this->selectedLocales as $locale)
                    @php($language = config('app.available_locales', [])[$locale] ?? $locale)
                    <flux:table.column>{{ $language }}</flux:table.column>
                @endforeach
            </flux:table.columns>
            <flux:table.rows>
                @forelse($keys as $index => $key)
                    <flux:table.row>
                        <flux:table.cell class="max-w-64">
                            <p class="text-wrap">{{ $key }}</p>
                        </flux:table.cell>
                        @foreach($this->selectedLocales as $locale)
                            <flux:table.cell>
                                <flux:textarea
                                    wire:model.defer="translationsData.{{ $index }}.{{ $locale }}"
                                    class="w-full"
                                    rows="2"
                                />
                            </flux:table.cell>
                        @endforeach
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="{{ count($this->selectedLocales) + 1 }}">
                            <x-empty-state
                                :title="__('No translations found')"
                                :description="__('There are no translations to display. Try changing your filters.')"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <div>
            {{ $keys->links() }}
        </div>
    </div>

    <flux:modal name="import-modal" :dismissible="false">
        <flux:card>
            <form wire:submit="import">
                <div class="p-6 space-y-4">
                    <flux:heading size="lg">{{ __('Import from Excel/CSV') }}</flux:heading>
                    <p class="text-zinc-500">{{ __('Upload a file to import translations. This will overwrite existing translations with matching keys.') }}</p>
                    <flux:input type="file" wire:model="file" />
                    <flux:error name="file" />
                </div>
                <div class="p-6 bg-zinc-50 flex justify-end gap-3">
                    <flux:modal.close>
                        <flux:button type="button" variant="ghost">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">{{ __('Import') }}</flux:button>
                </div>
            </form>
        </flux:card>
    </flux:modal>
</div>
