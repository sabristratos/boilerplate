<div>
    <flux:heading size="xl">{{ __('Attachment Management') }}</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-1">
            @can('create-attachments')
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Upload New File') }}</flux:heading>

                <form wire:submit.prevent="uploadFile"
                      x-data="{ uploading: false, progress: 0, previewUrl: null, isImage: false }"
                      x-on:livewire-upload-start="uploading = true"
                      x-on:livewire-upload-finish="uploading = false; progress = 0;"
                      x-on:livewire-upload-error="uploading = false; progress = 0;"
                      x-on:livewire-upload-progress="progress = $event.detail.progress">
                    <div class="space-y-4">
                        <div x-show="previewUrl" class="mb-4">
                            <template x-if="isImage">
                                <a x-bind:href="previewUrl" class="glightbox" data-gallery="upload-preview">
                                    <img x-bind:src="previewUrl" class="max-h-48 rounded-lg mx-auto object-contain" alt="{{ __('Preview') }}" />
                                </a>
                            </template>
                            <template x-if="!isImage && previewUrl">
                                <p class="text-sm text-center text-gray-500 dark:text-gray-400">{{ __('Preview not available for this file type.') }}</p>
                            </template>
                        </div>

                        <flux:field>
                            <flux:label for="fileUpload">{{ __('File') }}</flux:label>
                            <input
                                type="file"
                                id="fileUpload"
                                x-ref="fileInput"
                                wire:model="file"
                                x-on:change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        isImage = file.type.startsWith('image/');
                                        previewUrl = URL.createObjectURL(file);
                                    } else {
                                        previewUrl = null;
                                        isImage = false;
                                    }
                                "
                                class="block w-full text-sm text-gray-500 dark:text-gray-300
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-700 dark:file:text-primary-200
                                    hover:file:bg-primary-100 dark:hover:file:bg-primary-600"
                            />
                            @error('file') <flux:error :message="$message" /> @enderror
                        </flux:field>

                        <div x-show="uploading" class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="bg-primary-600 h-2.5 rounded-full" x-bind:style="`width: ${progress}%`"></div>
                            </div>
                            <div class="flex justify-between mt-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${progress}% {{ __('uploaded') }}`"></span>
                                <button
                                    type="button"
                                    x-on:click.prevent="$wire.cancelUpload('file'); previewUrl = null; isImage = false; if($refs.fileInput) $refs.fileInput.value = null;"
                                    class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                    {{ __('Cancel') }}
                                </button>
                            </div>
                        </div>

                        <flux:input
                            label="{{ __('Collection (optional)') }}"
                            wire:model="collection"
                            placeholder="{{ __('e.g., profile-images, documents') }}"
                        />

                        <flux:button
                            type="submit"
                            variant="primary"
                            class="w-full"
                            wire:loading.attr="disabled"
                            wire:target="file">
                            <span wire:loading.remove wire:target="file">{{ __('Upload') }}</span>
                            <span wire:loading wire:target="file">{{ __('Uploading...') }}</span>
                        </flux:button>
                    </div>
                </form>
            </flux:card>
            @endcan
        </div>

        <div class="md:col-span-2">
            <div class="mb-4 flex justify-between items-center">
                <flux:heading size="lg">{{ __('Uploaded Attachments') }}</flux:heading>
                <div class="flex items-center space-x-2">
                    <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search files...') }}" icon="magnifying-glass" class="max-w-xs" />
                    <flux:select wire:model.live="perPage" class="w-auto">
                        <option value="10">10 {{ __('per page') }}</option>
                        <option value="25">25 {{ __('per page') }}</option>
                        <option value="50">50 {{ __('per page') }}</option>
                    </flux:select>
                </div>
            </div>

            @if($attachments->isEmpty())
                <x-empty-state
                    icon="document-arrow-up"
                    heading="{{ __('No attachments found') }}"
                    description="{{ $search ? __('Try a different search term.') : __('Upload your first file to get started.') }}"
                />
            @else
                <div class="space-y-3">
                    @foreach($attachments as $attachment)
                        <x-attachments.item :attachment="$attachment" :show-actions="false" class="dark:bg-zinc-800 dark:border-zinc-700">
                            <x-slot:actions>
                                <flux:dropdown align="end">
                                    <flux:button size="sm" variant="outline" icon:trailing="chevron-down" class="ml-2 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                                        {{ __('Actions') }}
                                    </flux:button>
                                    <flux:menu>
                                        @if($attachment->isImage())
                                            <flux:menu.item icon="eye" as="a" href="{{ $attachment->url }}" class="glightbox" data-gallery="attachments-list" data-title="{{ $attachment->filename }}">
                                                {{ __('View Image') }}
                                            </flux:menu.item>
                                        @else
                                            <flux:menu.item icon="eye" as="a" href="{{ $attachment->url }}" target="_blank">
                                                {{ __('View File') }}
                                            </flux:menu.item>
                                        @endif
                                        <flux:menu.item icon="arrow-down-tray" as="a" href="{{ $attachment->url }}" download="{{ $attachment->filename }}">
                                            {{ __('Download') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="arrow-path" wire:click="showReplaceModal({{ $attachment->id }})">
                                            {{ __('Replace') }}
                                        </flux:menu.item>
                                        <flux:menu.item icon="clipboard-document" x-on:click="
                                                navigator.clipboard.writeText('{{ Storage::disk($attachment->disk)->url($attachment->path) }}');
                                                Flux.toast({ heading: '{{ __('Success') }}', text: '{{ __('File URL copied to clipboard!') }}', variant: 'success' });
                                            ">
                                            {{ __('Copy URL') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        @can('delete-attachments')
                                        <flux:menu.item
                                            icon="trash"
                                            variant="danger"
                                            wire:click="delete({{ $attachment->id }})"
                                            wire:confirm="{{ __('Are you sure you want to delete this file?') }} {{ $attachment->filename }}"
                                        >
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                        @endcan
                                    </flux:menu>
                                </flux:dropdown>
                            </x-slot:actions>
                        </x-attachments.item>
                    @endforeach
                </div>
                @if ($attachments->hasPages())
                    <div class="mt-6">
                        {{ $attachments->links('livewire.flux-pagination') }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    <flux:modal wire:model.defer="showingReplaceModal" class="md:w-96">
        <flux:heading size="lg">
            {{ __('Replace Attachment') }}
            @if($attachmentToReplace)
                <span class="block text-sm font-normal text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Replacing:') }} <strong>{{ $attachmentToReplace->filename }}</strong>
                    ({{ Str::upper(pathinfo($attachmentToReplace->filename, PATHINFO_EXTENSION)) }},
                    {{ \Illuminate\Support\Number::fileSize($attachmentToReplace->size, precision: 2) }})
                </span>
            @endif
        </flux:heading>

        <form wire:submit.prevent="processReplace" class="mt-6"
              x-data="{ uploadingReplace: false, progressReplace: 0, previewUrlReplace: null, isImageReplace: false }"
              x-on:livewire-upload-start="if ($event.detail.id === $wire.__instance.id + '-replacementFile') uploadingReplace = true"
              x-on:livewire-upload-finish="if ($event.detail.id === $wire.__instance.id + '-replacementFile') { uploadingReplace = false; progressReplace = 0; }"
              x-on:livewire-upload-error="if ($event.detail.id === $wire.__instance.id + '-replacementFile') { uploadingReplace = false; progressReplace = 0; }"
              x-on:livewire-upload-progress="if ($event.detail.id === $wire.__instance.id + '-replacementFile') progressReplace = $event.detail.progress">
            <div class="space-y-4">
                <div x-show="previewUrlReplace" class="mb-4">
                    <template x-if="isImageReplace">
                        <a x-bind:href="previewUrlReplace" class="glightbox" data-gallery="replace-preview">
                            <img x-bind:src="previewUrlReplace" class="max-h-48 rounded-lg mx-auto object-contain" alt="{{ __('Replacement Preview') }}" />
                        </a>
                    </template>
                    <template x-if="!isImageReplace && previewUrlReplace">
                        <p class="text-sm text-center text-gray-500 dark:text-gray-400">{{ __('Preview not available for this file type.') }}</p>
                    </template>
                </div>
                <flux:field>
                    <flux:label for="replacementFile">{{ __('New File') }}</flux:label>
                    <input
                        type="file"
                        id="replacementFile"
                        x-ref="replacementFileInput"
                        wire:model="replacementFile"
                        x-on:change="
                            const file = $event.target.files[0];
                            if (file) {
                                isImageReplace = file.type.startsWith('image/');
                                previewUrlReplace = URL.createObjectURL(file);
                            } else {
                                previewUrlReplace = null;
                                isImageReplace = false;
                            }
                        "
                        class="block w-full text-sm text-gray-500 dark:text-gray-300
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary-50 file:text-primary-700 dark:file:bg-primary-700 dark:file:text-primary-200
                            hover:file:bg-primary-100 dark:hover:file:bg-primary-600"
                    />
                    @error('replacementFile') <flux:error :message="$message" /> @enderror
                </flux:field>

                <div x-show="uploadingReplace" class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                        <div class="bg-primary-600 h-2.5 rounded-full" x-bind:style="`width: ${progressReplace}%`"></div>
                    </div>
                    <div class="flex justify-between mt-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${progressReplace}% {{ __('uploaded') }}`"></span>
                        <button
                            type="button"
                            x-on:click.prevent="$wire.cancelUpload('replacementFile'); previewUrlReplace = null; isImageReplace = false; if ($refs.replacementFileInput) $refs.replacementFileInput.value = null;"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                            {{ __('Cancel') }}
                        </button>
                    </div>
                </div>

                @if($attachmentToReplace)
                    <flux:callout variant="warning" icon="exclamation-triangle" class="text-sm">
                        <flux:callout.text>
                        {{ __('Replacing this file will overwrite the existing file on the server. The URL will remain the same. This action cannot be undone.') }}
                        {{ __('The new file\'s original name will be stored, but the path on disk (and thus the URL) will not change.') }}
                        </flux:callout.text>
                        </flux:callout>
                @endif
            </div>

            <div class="flex justify-end space-x-3 mt-8">
                <flux:button type="button" variant="outline" wire:click="closeReplaceModal" x-bind:disabled="uploadingReplace" class="dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-700">
                    {{ __('Cancel') }}
                </flux:button>
                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="replacementFile">
                    <span wire:loading.remove wire:target="replacementFile">{{ __('Save Replacement') }}</span>
                    <span wire:loading wire:target="replacementFile">{{ __('Replacing...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
