<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">
            {{ $legalPage->exists ? __('Edit Page') : __('Create Page') }}
        </flux:heading>
        <flux:button :href="route('admin.legal.index')" variant="outline" icon="arrow-left" tooltip="{{ __('Back to Legal Pages') }}">
            {{ __('Back to Legal Pages') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <form wire:submit.prevent="save" class="w-full">
        <flux:tab.group>
            <flux:tabs wire:model.live="currentLocale">
                @foreach($locales as $localeCode => $localeName)
                    <flux:tab :name="$localeCode">{{ $localeName }}</flux:tab>
                @endforeach
            </flux:tabs>

            @foreach($locales as $localeCode => $localeName)
                <flux:tab.panel :name="$localeCode">
                    <div class="space-y-4 mt-6">
                        <flux:input wire:model.lazy="title.{{ $localeCode }}" label="{{ __('Title') }}" description="{{ __('The main title of the legal page.') }}" :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" />
                        <flux:input wire:model.lazy="slug.{{ $localeCode }}" label="{{ __('Slug') }}" description="{{ __('The URL-friendly version of the title.') }}" :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" />
                        <div wire:ignore>
                            <flux:editor wire:model="content.{{ $localeCode }}" label="{{ __('Content') }}" description="{{ __('The main content of the legal page.') }}" :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')" />
                        </div>
                    </div>
                </flux:tab.panel>
            @endforeach
        </flux:tab.group>

        <div class="mt-6">
            <flux:card>
                <flux:switch 
                    wire:model="is_published"
                    label="{{ __('Published') }}"
                    description="{{ __('Toggles the visibility of the page to the public.') }}" 
                    :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')"
                />
            </flux:card>
        </div>

        <div class="md:col-span-3 flex justify-end space-x-3 mt-8">
            <flux:button type="button" variant="outline" :href="route('admin.legal.index')">
                {{ __('Cancel') }}
            </flux:button>
            <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save" :disabled="!auth()->user()->can($legalPage->exists ? 'edit-legal-pages' : 'create-legal-pages')">
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
