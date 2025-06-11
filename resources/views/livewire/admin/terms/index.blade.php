<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-2">
            <flux:button :href="route('admin.taxonomies.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Taxonomies') }}" />
            <flux:heading size="xl">
                {{ __('Terms for') }} <span class="font-semibold">{{ $taxonomy->name }}</span>
            </flux:heading>
        </div>
        @can('create-terms')
        <flux:button variant="primary" icon="plus" :href="route('admin.taxonomies.terms.create', $taxonomy)">
            {{ __('Create Term') }}
        </flux:button>
        @endcan
    </div>

    <flux:separator variant="subtle" class="my-8" />

    @if($terms->total() === 0 && !$this->hasFilters())
        <x-empty-state
            icon="tag"
            heading="{{ __('No terms yet') }}"
            description="{{ __('Get started by creating your first term.') }}">
            @can('create-terms')
            <flux:button variant="primary" icon="plus" :href="route('admin.taxonomies.terms.create', $taxonomy)">{{ __('Create Term') }}</flux:button>
            @endcan
        </x-empty-state>
    @else
        <div class="flex justify-between mb-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.500ms="search" placeholder="{{ __('Search...') }}" icon="magnifying-glass" />
            </div>
            <div class="ml-4 flex space-x-2">
                <flux:select wire:model.live="perPage">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </flux:select>
            </div>
        </div>
        @if($terms->isEmpty())
            <x-empty-state
                icon="magnifying-glass"
                heading="{{ __('No terms found') }}"
                description="{{ __('Try adjusting your search to find what you\'re looking for.') }}"
            />
        @else
            <flux:table :paginate="$terms">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'name'" :direction="$sortDirection" wire:click="sort('name')">{{ __('Name') }}</flux:table.column>
                    <flux:table.column>{{ __('Children') }}</flux:table.column>
                    <flux:table.column />
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($terms as $term)
                        <flux:table.row :key="$term->id">
                            <flux:table.cell>
                                {{ $term->name }}
                                <div class="text-sm text-gray-500">{{ $term->description }}</div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="info">{{ $term->children_count }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon="ellipsis-vertical" variant="ghost" tooltip="{{ __('Actions') }}" />
                                    <flux:menu>
                                        @can('edit-terms')
                                        <flux:menu.item :href="route('admin.taxonomies.terms.edit', ['taxonomy' => $taxonomy, 'term' => $term])" icon="pencil">{{ __('Edit') }}</flux:menu.item>
                                        @endcan
                                        @can('delete-terms')
                                        <flux:menu.item wire:click="$dispatch('confirm-delete-term', { term: {{ $term->id }} })" icon="trash">{{ __('Delete') }}</flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>

            <div class="mt-4">
                {{ $terms->links() }}
            </div>
        @endif
    @endif

    <flux:modal wire:model="confirmingDelete" class="md:w-96">
        @if ($deletingTerm)
            <flux:heading size="lg">{{ __('Delete Term: ') . $deletingTerm->name }}</flux:heading>
            <p class="mt-2">{{ __('Are you sure you want to delete this term? This action cannot be undone.') }}</p>
            <div class="flex justify-end space-x-3 mt-8">
                <flux:button variant="outline" wire:click="$set('confirmingDelete', false)">{{ __('Cancel') }}</flux:button>
                <flux:button variant="danger" wire:click="delete">{{ __('Delete') }}</flux:button>
            </div>
        @endif
    </flux:modal>
</div> 