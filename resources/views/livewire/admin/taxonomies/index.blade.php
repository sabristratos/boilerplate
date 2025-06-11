<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">{{ __('Taxonomies') }}</flux:heading>
        @can('create-taxonomies')
        <flux:button variant="primary" icon="plus" :href="route('admin.taxonomies.create')">
            {{ __('Create Taxonomy') }}
        </flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="my-8" />
    
    @if ($taxonomies->total() === 0 && !$this->hasFilters())
        <x-empty-state
            icon="tag"
            heading="{{ __('No taxonomies yet') }}"
            description="{{ __('Get started by creating your first taxonomy.') }}">
            @can('create-taxonomies')
            <flux:button variant="primary" icon="plus" :href="route('admin.taxonomies.create')">{{ __('Create Taxonomy') }}</flux:button>
            @endcan
        </x-empty-state>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
            <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
            <flux:select wire:model.live="isHierarchical">
                <option value="">{{ __('Any Type') }}</option>
                <option value="yes">{{ __('Hierarchical') }}</option>
                <option value="no">{{ __('Flat') }}</option>
            </flux:select>
            <flux:select wire:model.live="perPage">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </flux:select>
        </div>
        @if($taxonomies->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No taxonomies found') }}"
                description="{{ __('Try adjusting your search to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$taxonomies">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Term Preview') }}</flux:table.column>
                    <flux:table.column>{{ __('Total Terms') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($taxonomies as $taxonomy)
                        <flux:table.row :key="$taxonomy->id">
                            <flux:table.cell>
                                <a href="{{ route('admin.taxonomies.terms.index', $taxonomy) }}" class="font-medium text-primary-600 hover:underline">
                                    {{ $taxonomy->name }}
                                </a>
                                <div class="text-sm text-gray-500">{{ $taxonomy->description }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge :variant="$taxonomy->hierarchical ? 'primary' : 'secondary'">
                                    {{ $taxonomy->hierarchical ? __('Hierarchical') : __('Flat') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($taxonomy->terms->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($taxonomy->terms as $term)
                                            <flux:badge variant="gray">{{ $term->name }}</flux:badge>
                                        @endforeach
                                        @if($taxonomy->terms_count > 3)
                                            <flux:badge variant="gray">...</flux:badge>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-500">{{ __('No terms') }}</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="info">{{ $taxonomy->terms_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical" variant="ghost" tooltip="{{ __('Actions') }}" />
                                    <flux:menu>
                                        @can('view-terms')
                                        <flux:menu.item :href="route('admin.taxonomies.terms.index', $taxonomy)" icon="tag">{{ __('Manage Terms') }}</flux:menu.item>
                                        @endcan
                                        @can('edit-taxonomies')
                                        <flux:menu.item :href="route('admin.taxonomies.edit', $taxonomy)" icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                        @endcan
                                        @can('delete-taxonomies')
                                        <flux:menu.item wire:click="$dispatch('confirm-delete-taxonomy', { taxonomy: {{ $taxonomy->id }} })" icon="trash">{{ __('Delete') }}</flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $taxonomies->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="confirmingDelete" class="md:w-96">
        @if ($deletingTaxonomy)
            <flux:heading size="lg">{{ __('Delete Taxonomy: ') . $deletingTaxonomy->name }}</flux:heading>
            <p class="mt-2">{{ __('Are you sure you want to delete this taxonomy and all its terms? This action cannot be undone.') }}</p>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button variant="outline" wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        @endif
    </flux:modal>
</div> 