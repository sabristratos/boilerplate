<div>
    <div class="flex justify-between items-center">
        <flux:heading size="xl">{{ __('Taxonomy Management') }}</flux:heading>
        <flux:button wire:click="openCreateTaxonomyModal" variant="primary" icon="plus">{{ __('Create Taxonomy') }}</flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <div class="mb-6">
        <flux:input
            wire:model.live.debounce.300ms="search"
            placeholder="{{ __('Search taxonomies or terms...') }}"
            icon="magnifying-glass"
            class="w-full md:w-1/3"
        />
    </div>

    <div class="grid grid-cols-1 @if($selectedTaxonomyId && $currentTaxonomy) lg:grid-cols-2 @endif gap-8">
        <div>
            <flux:heading size="lg" class="mb-4">{{ __('Available Taxonomies') }}</flux:heading>
            @if($taxonomies->isEmpty())
                <x-flux.empty-state
                    icon="tag"
                    heading="{{ __('No taxonomies found') }}"
                    description="{{ $search && !$selectedTaxonomyId ? __('Try a different search term.') : __('Create your first taxonomy to get started.') }}"
                />
            @else
                <flux:table :paginate="$taxonomies" paginator-name="taxonomiesPage">
                    <flux:table.columns>
                        <flux:table.column>{{ __('Name') }}</flux:table.column>
                        <flux:table.column>{{ __('Hierarchical') }}</flux:table.column>
                        <flux:table.column class="text-center">{{ __('Terms') }}</flux:table.column>
                        <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($taxonomies as $taxonomy)
                            <flux:table.row wire:key="taxonomy-{{ $taxonomy->id }}" class="{{ $selectedTaxonomyId == $taxonomy->id ? 'bg-primary-50 dark:bg-primary-800/20' : '' }}">
                                <flux:table.cell>
                                    <button type="button" wire:click="selectTaxonomyToManageTerms({{ $taxonomy->id }})" class="font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                        {{ $taxonomy->name }}
                                    </button>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $taxonomy->slug }}</div>
                                    @if($taxonomy->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($taxonomy->description, 70) }}</div>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if($taxonomy->hierarchical)
                                        <flux:badge color="green" size="sm">{{ __('Yes') }}</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">{{ __('No') }}</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="text-center">
                                    <flux:badge color="blue" size="sm">{{ $taxonomy->terms_count }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex space-x-1 justify-end">
                                        <flux:button size="sm" variant="outline" wire:click="openEditTaxonomyModal({{ $taxonomy->id }})" icon="pencil-square" tooltip="{{__('Edit Taxonomy')}}" />
                                        <flux:button size="sm" variant="{{ $selectedTaxonomyId == $taxonomy->id ? 'primary' : 'outline' }}" wire:click="selectTaxonomyToManageTerms({{ $taxonomy->id }})" icon="list-bullet" tooltip="{{__('Manage Terms')}}" />
                                        <flux:button size="sm" variant="danger" wire:click="deleteTaxonomy({{ $taxonomy->id }})" icon="trash" wire:confirm="{{__('Are you sure you want to delete this taxonomy and all its terms?')}}" tooltip="{{__('Delete Taxonomy')}}" />
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
                @if ($taxonomies->hasPages())
                    <div class="mt-6">
                        <flux:pagination :paginator="$taxonomies" />
                    </div>
                @endif
            @endif
        </div>

        @if($selectedTaxonomyId && $currentTaxonomy)
            <div>
                <div class="flex justify-between items-center mb-4">
                    <flux:heading size="lg">
                        {{ __('Terms for') }} <span class="text-primary-600 dark:text-primary-400">{{ $currentTaxonomy->name }}</span>
                    </flux:heading>
                    <flux:button wire:click="openCreateTermModal" variant="primary" icon="plus">{{ __('Add Term') }}</flux:button>
                </div>

                @if($terms->isEmpty())
                    <x-flux.empty-state
                        icon="tag"
                        heading="{{ __('No terms found for this taxonomy') }}"
                        description="{{ $search ? __('Try a different search term.') : __('Add your first term to this taxonomy to get started.') }}"
                    >
                        <flux:button wire:click="openCreateTermModal">{{__('Add New Term')}}</flux:button>
                    </x-flux.empty-state>
                @else
                    <flux:table :paginate="$terms" paginator-name="termsPage">
                        <flux:table.columns>
                            <flux:table.column>{{ __('Name') }}</flux:table.column>
                            @if($currentTaxonomy->hierarchical)
                                <flux:table.column>{{ __('Parent') }}</flux:table.column>
                            @endif
                            <flux:table.column class="text-center">{{ __('Order') }}</flux:table.column>
                            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach($terms as $term)
                                <flux:table.row wire:key="term-{{ $term->id }}">
                                    <flux:table.cell>
                                        {{ $term->name }}
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $term->slug }}</div>
                                    </flux:table.cell>
                                    @if($currentTaxonomy->hierarchical)
                                        <flux:table.cell>
                                            @if($term->parent)
                                                <flux:badge color="blue" size="sm">{{ $term->parent->name }}</flux:badge>
                                            @else
                                                <span class="text-xs text-gray-400 dark:text-gray-500 italic">--</span>
                                            @endif
                                        </flux:table.cell>
                                    @endif
                                    <flux:table.cell class="text-center">{{ $term->order }}</flux:table.cell>
                                    <flux:table.cell align="end">
                                        <div class="flex space-x-1 justify-end">
                                            <flux:button size="sm" variant="outline" wire:click="openEditTermModal({{ $term->id }})" icon="pencil-square" tooltip="{{__('Edit Term')}}" />
                                            <flux:button size="sm" variant="danger" wire:click="deleteTerm({{ $term->id }})" icon="trash" wire:confirm="{{__('Are you sure you want to delete this term?')}}" tooltip="{{__('Delete Term')}}" />
                                        </div>
                                    </flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                    @if ($terms->hasPages())
                        <div class="mt-6">
                            <flux:pagination :paginator="$terms" />
                        </div>
                    @endif
                @endif
            </div>
        @endif
    </div>

    <flux:modal wire:model.defer="showModal" class="md:w-[36rem]">
        @if($isCreatingTaxonomy || $isEditingTaxonomy)
            <flux:heading size="lg" class="mb-6">
                {{ $isCreatingTaxonomy ? __('Create New Taxonomy') : __('Edit Taxonomy') }}
            </flux:heading>
            <form wire:submit.prevent="{{ $isCreatingTaxonomy ? 'storeTaxonomy' : 'updateTaxonomy' }}">
                <div class="space-y-4">
                    <flux:input
                        label="{{ __('Taxonomy Name') }}"
                        wire:model="taxonomyName"
                        placeholder="{{ __('e.g., Categories, Product Tags') }}"
                        required
                        :error="$errors->first('taxonomyName')"
                    />
                    <flux:textarea
                        label="{{ __('Description (optional)') }}"
                        wire:model="taxonomyDescription"
                        rows="3"
                        :error="$errors->first('taxonomyDescription')"
                    />
                    <flux:checkbox
                        label="{{ __('Enable hierarchical structure (parent-child terms)') }}"
                        wire:model="taxonomyHierarchical"
                        :error="$errors->first('taxonomyHierarchical')"
                    />
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <flux:button type="button" variant="outline" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $isCreatingTaxonomy ? __('Create Taxonomy') : __('Save Changes') }}
                    </flux:button>
                </div>
            </form>

        @elseif($isCreatingTerm || $isEditingTerm)
            <flux:heading size="lg" class="mb-6">
                {{ $isCreatingTerm ? __('Create New Term') : __('Edit Term') }}
                @if($currentTaxonomy)
                    <span class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('for') }} {{ $currentTaxonomy->name }}</span>
                @endif
            </flux:heading>
            <form wire:submit.prevent="{{ $isCreatingTerm ? 'storeTerm' : 'updateTerm' }}">
                <div class="space-y-4">
                    <flux:input
                        label="{{ __('Term Name') }}"
                        wire:model="termName"
                        placeholder="{{ __('e.g., PHP, Laravel, Frontend') }}"
                        required
                        :error="$errors->first('termName')"
                    />
                    <flux:textarea
                        label="{{ __('Description (optional)') }}"
                        wire:model="termDescription"
                        rows="3"
                        :error="$errors->first('termDescription')"
                    />
                    @if($currentTaxonomy && $currentTaxonomy->hierarchical)
                        <flux:select
                            label="{{ __('Parent Term (optional)') }}"
                            wire:model="termParentId"
                            :error="$errors->first('termParentId')"
                        >
                            <option value="">{{ __('-- None (top level) --') }}</option>
                            @foreach($availableParentTerms as $parentTerm)
                                <option value="{{ $parentTerm->id }}">{{ $parentTerm->name }}</option>
                            @endforeach
                        </flux:select>
                    @endif
                    <flux:input
                        type="number"
                        label="{{ __('Order (optional)') }}"
                        wire:model="termOrder"
                        min="0"
                        :error="$errors->first('termOrder')"
                    />
                </div>
                <div class="flex justify-end space-x-3 mt-8">
                    <flux:button type="button" variant="outline" wire:click="closeModal">{{ __('Cancel') }}</flux:button>
                    <flux:button type="submit" variant="primary">
                        {{ $isCreatingTerm ? __('Create Term') : __('Save Changes') }}
                    </flux:button>
                </div>
            </form>
        @endif
    </flux:modal>
</div>
