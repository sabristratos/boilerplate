<div>
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-4">
            <flux:button :href="route('admin.legal-pages.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Legal Pages') }}">
                {{ __('Back to Legal Pages') }}
            </flux:button>
            <flux:heading size="xl">
                {{ $legalPage->exists ? __('Edit Page') : __('Create Page') }}
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
                <flux:label>{{ __('Title') }}</flux:label>
                <flux:description>{{ __('The main title of the legal page.') }}</flux:description>
                <flux:input 
                    wire:key="legal-title-{{ $currentLocale }}"
                    wire:model.defer="title.{{ $currentLocale }}" 
                    :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" 
                />
                <flux:error name="title.{{ $currentLocale }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Slug') }}</flux:label>
                <flux:description>{{ __('The URL-friendly version of the title.') }}</flux:description>
                <flux:input 
                    wire:key="legal-slug-{{ $currentLocale }}"
                    wire:model.defer="slug.{{ $currentLocale }}" 
                    :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" 
                />
                <flux:error name="slug.{{ $currentLocale }}" />
            </flux:field>

            <flux:field>
                <flux:label>{{ __('Content') }}</flux:label>
                <flux:description>{{ __('The main content of the legal page.') }}</flux:description>
                <div wire:ignore>
                    <flux:editor 
                        wire:key="legal-content-{{ $currentLocale }}"
                        wire:model.defer="content.{{ $currentLocale }}" 
                        :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" 
                    />
                </div>
                <flux:error name="content.{{ $currentLocale }}" />
            </flux:field>
        </div>

        <div class="mt-8">
            <flux:field>
                <flux:label>{{ __('Published') }}</flux:label>
                <flux:description>{{ __('Toggles the visibility of the page to the public.') }}</flux:description>
                <flux:switch 
                    wire:model="is_published"
                    :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')"
                />
                <flux:error name="is_published" />
            </flux:field>
        </div>

        <div class="flex justify-end space-x-3 pt-4">
            <flux:button type="button" variant="ghost" :href="route('admin.legal-pages.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')">
                <span wire:loading.remove wire:target="save">
                    {{ $legalPage->exists ? __('Save Changes') : __('Create Page') }}
                </span>
                <span wire:loading wire:target="save">
                    {{ $legalPage->exists ? __('Saving...') : __('Creating...') }}
                </span>
            </flux:button>
        </div>
    </form>
</div>
