<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button :href="route('admin.taxonomies.terms.index', $taxonomy)" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Terms') }}" />
            <flux:heading size="xl">
                {{ $term->exists ? __('Edit Term') : __('Create Term') }}
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
                <flux:description>{{ __('The name of the term.') }}</flux:description>
                <flux:input
                    wire:key="term-name-{{ $currentLocale }}"
                    wire:model.defer="name.{{ $currentLocale }}"
                    :disabled="!auth()->user()->can($term->exists ? 'edit-terms' : 'create-terms')"
                />
                <flux:error name="name.{{ $currentLocale }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Description') }}</flux:label>
                <flux:description>{{ __('A brief description of the term.') }}</flux:description>
                <flux:textarea
                    wire:key="term-description-{{ $currentLocale }}"
                    wire:model.defer="description.{{ $currentLocale }}"
                    :disabled="!auth()->user()->can($term->exists ? 'edit-terms' : 'create-terms')"
                />
                <flux:error name="description.{{ $currentLocale }}" />
            </flux:field>

            @if($taxonomy->is_hierarchical)
                <flux:field>
                    <flux:label>{{ __('Parent Term') }}</flux:label>
                    <flux:description>{{ __('The parent term in the hierarchy.') }}</flux:description>
                    <flux:select
                        wire:model.lazy="parent_id"
                        :disabled="!auth()->user()->can($term->exists ? 'edit-terms' : 'create-terms')"
                    >
                        <option value="">{{ __('None') }}</option>
                        @foreach($parentTerms as $parentTerm)
                            <option value="{{ $parentTerm->id }}">{{ $parentTerm->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="parent_id" />
                </flux:field>
            @endif
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <flux:button type="button" variant="ghost" :href="route('admin.taxonomies.terms.index', $taxonomy)">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" :disabled="!auth()->user()->can($term->exists ? 'edit-terms' : 'create-terms')">
                <span wire:loading.remove wire:target="save">
                    {{ $term->exists ? __('Save Changes') : __('Create Term') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $term->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div> 