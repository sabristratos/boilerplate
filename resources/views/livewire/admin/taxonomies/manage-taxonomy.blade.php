<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button :href="route('admin.taxonomies.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Taxonomies') }}">
                {{ __('Back to Taxonomies') }}
            </flux:button>
            <flux:heading size="xl">
                {{ $taxonomy->exists ? __('Edit Taxonomy') : __('Create Taxonomy') }}
            </flux:heading>
        </div>
        <div class="flex items-center gap-2">
            <flux:icon name="globe-alt" class="w-5 h-5 text-zinc-400" />
            <flux:select wire:model.live="currentLocale" class="w-40">
                @foreach($locales as $localeCode => $localeName)
                    <option value="{{ $localeCode }}">{{ $localeName }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="max-w-3xl">
        <div class="space-y-6">
            <flux:field>
                <flux:label>{{ __('Name') }}</flux:label>
                <flux:description>{{ __('The name of the taxonomy.') }}</flux:description>
                <flux:input
                    wire:key="taxonomy-name-{{ $currentLocale }}"
                    wire:model.defer="name.{{ $currentLocale }}"
                    :disabled="!auth()->user()->can($taxonomy->exists ? 'edit-taxonomies' : 'create-taxonomies')"
                />
                <flux:error name="name.{{ $currentLocale }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:description>{{ __('A brief description of the taxonomy.') }}</flux:description>
                <flux:input
                    wire:key="taxonomy-description-{{ $currentLocale }}"
                    wire:model.defer="description.{{ $currentLocale }}"
                    :disabled="!auth()->user()->can($taxonomy->exists ? 'edit-taxonomies' : 'create-taxonomies')"
                />
                <flux:error name="description.{{ $currentLocale }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Hierarchical') }}</flux:label>
                <flux:description>{{ __('Whether terms in this taxonomy can have parent-child relationships.') }}</flux:description>
                <flux:switch 
                    wire:model="hierarchical"
                    :disabled="!auth()->user()->can($taxonomy->exists ? 'edit-taxonomies' : 'create-taxonomies')"
                />
                <flux:error name="hierarchical" />
            </flux:field>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <flux:button type="button" variant="ghost" :href="route('admin.taxonomies.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" :disabled="!auth()->user()->can($taxonomy->exists ? 'edit-taxonomies' : 'create-taxonomies')">
                <span wire:loading.remove wire:target="save">
                    {{ $taxonomy->exists ? __('Save Changes') : __('Create Taxonomy') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $taxonomy->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 