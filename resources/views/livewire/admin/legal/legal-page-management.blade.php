<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Legal Pages') }}</flux:heading>
        @can('create-legal-pages')
        <flux:button variant="primary" icon="plus" :href="route('admin.legal-pages.create')">{{ __('Create Page') }}</flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="my-8" />

    @if ($pages->total() === 0 && !$this->hasFilters())
        <x-empty-state
            icon="document-text"
            heading="{{ __('No pages yet') }}"
            description="{{ __('Get started by creating your first legal page.') }}">
            @can('create-legal-pages')
            <flux:button variant="primary" icon="plus" :href="route('admin.legal-pages.create')">{{ __('Create Page') }}</flux:button>
            @endcan
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="localeFilter">
                <option value="">{{ __('All Locales') }}</option>
                @foreach($locales as $code => $name)
                    <option value="{{ $code }}">{{ $name }}</option>
                @endforeach
            </flux:select>
            <flux:select wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </flux:select>
        </div>
        @if($pages->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No pages found') }}"
                description="{{ __('Try adjusting your search or filter to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$pages">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'title'" :direction="$sortDirection" wire:click="sort('title')">Title</flux:table.column>
                    <flux:table.column sortable :sorted="$sortBy === 'slug'" :direction="$sortDirection" wire:click="sort('slug')">Slug</flux:table.column>
                    <flux:table.column>Locale</flux:table.column>
                    <flux:table.column>Published</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($pages as $page)
                        <flux:table.row :key="$page->id">
                            <flux:table.cell>
                                {{ $this->localeFilter ? $page->getTranslation('title', $this->localeFilter) : ($page->title ?: current($page->getTranslations('title'))) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $this->localeFilter ? $page->getTranslation('slug', $this->localeFilter) : ($page->slug ?: current($page->getTranslations('slug'))) }}
                            </flux:table.cell>
                            <flux:table.cell>
                                {{ $page->available_locales_as_string }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :color="$page->is_published ? 'green' : 'red'">
                                    {{ $page->is_published ? 'Yes' : 'No' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical" variant="ghost" tooltip="{{ __('Actions') }}" />
                                    <flux:menu>
                                        @can('edit-legal-pages')
                                        <flux:menu.item :href="route('admin.legal-pages.edit', ['legalPage' => $page, 'tab' => $this->localeFilter ?? $page->first_available_locale])" icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                        @endcan
                                        @can('delete-legal-pages')
                                        <flux:menu.item wire:click="$dispatch('confirm-delete-page', { legalPage: {{ $page->id }} })" icon="trash">{{ __('Delete') }}</flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $pages->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="confirmingDelete" class="md:w-96">
        @if ($deletingPage)
            <flux:heading size="lg">{{ __('Delete Page: ') . $deletingPage->title }}</flux:heading>
            <p class="mt-2">{{ __('Are you sure you want to delete this page? This action cannot be undone.') }}</p>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button variant="outline" wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        @endif
    </flux:modal>
</div>
