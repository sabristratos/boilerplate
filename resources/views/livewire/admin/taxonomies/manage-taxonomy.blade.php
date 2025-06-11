<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">
            {{ $taxonomy?->exists ? __('Edit Taxonomy') : __('Create Taxonomy') }}
        </flux:heading>
        <flux:button :href="route('admin.taxonomies.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Taxonomies') }}">
            {{ __('Back to Taxonomies') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="w-full max-w-2xl">
        <div class="space-y-4">
            <flux:input wire:model="name" label="{{ __('Name') }}" required :disabled="!auth()->user()->can($taxonomy?->exists ? 'edit-taxonomies' : 'create-taxonomies')" />
            <flux:input 
                wire:model="description" 
                label="{{ __('Description') }}" 
                description="{{ __('A short description for this taxonomy.') }}"
                :disabled="!auth()->user()->can($taxonomy?->exists ? 'edit-taxonomies' : 'create-taxonomies')"
            />
            <flux:switch 
                wire:model="hierarchical" 
                label="{{ __('Hierarchical') }}"
                description="{{ __('Hierarchical taxonomies can have parent-child relationships, like categories.') }}"
                :disabled="!auth()->user()->can($taxonomy?->exists ? 'edit-taxonomies' : 'create-taxonomies')"
            />
        </div>

        <div class="flex justify-end space-x-3 mt-8">
            <flux:button type="button" variant="outline" :href="route('admin.taxonomies.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" :disabled="!auth()->user()->can($taxonomy?->exists ? 'edit-taxonomies' : 'create-taxonomies')">
                <span wire:loading.remove wire:target="save">
                    {{ $taxonomy?->exists ? __('Save Changes') : __('Create Taxonomy') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $taxonomy?->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 