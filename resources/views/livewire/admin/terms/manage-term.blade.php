<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-2">
            <flux:button :href="route('admin.taxonomies.terms.index', $taxonomy)" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Terms') }}" />
            <flux:heading size="xl">
                {{ $term?->exists ? __('Edit Term') : __('Create Term') }}
            </flux:heading>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:input wire:model="name" label="{{ __('Name') }}" required :disabled="!auth()->user()->can($term?->exists ? 'edit-terms' : 'create-terms')" />
            <flux:input 
                wire:model="description" 
                label="{{ __('Description') }}"
                description="{{ __('A short description for this term.') }}"
                :disabled="!auth()->user()->can($term?->exists ? 'edit-terms' : 'create-terms')"
            />
            @if($taxonomy->hierarchical)
                <div>
                    <flux:select wire:model="parent_id" label="{{ __('Parent') }}" :disabled="!auth()->user()->can($term?->exists ? 'edit-terms' : 'create-terms')">
                        <option value="">{{ __('None') }}</option>
                        @foreach($parentTerms as $parentTerm)
                            <option value="{{ $parentTerm->id }}">{{ $parentTerm->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:text size="sm" color="gray" class="mt-2">
                        {{ __('Assign a parent to create a hierarchy. Leave blank for a top-level term.') }}
                    </flux:text>
                </div>
            @endif
        </div>

        <div class="flex justify-end space-x-3 mt-8">
            <flux:button type="button" variant="outline" :href="route('admin.taxonomies.terms.index', $taxonomy)">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" :disabled="!auth()->user()->can($term?->exists ? 'edit-terms' : 'create-terms')">
                <span wire:loading.remove wire:target="save">
                    {{ $term?->exists ? __('Save Changes') : __('Create Term') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $term?->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 