<div>
    <form wire:submit.prevent="processUpload" class="space-y-4">
        @if($showLabel)
            <flux:heading size="md">{{ __($label) }}</flux:heading>
        @endif

        <div
            x-data="{
                uploading: false,
                progress: 0,
                isImage: false,
                previewUrl: null
            }"
            x-on:livewire-upload-start="uploading = true"
            x-on:livewire-upload-finish="uploading = false"
            x-on:livewire-upload-error="uploading = false"
            x-on:livewire-upload-progress="progress = $event.detail.progress"
            class="space-y-4"
        >
            <!-- File Preview (only for images) -->
            <div x-show="previewUrl" class="mb-4">
                <img x-bind:src="previewUrl" class="max-h-48 rounded-lg mx-auto" />
            </div>

            <flux:field>
                <flux:label>{{ __('File') }}</flux:label>
                <input
                    type="file"
                    wire:model="file"
                    x-on:change="
                        const file = $event.target.files[0];
                        if (file) {
                            isImage = file.type.includes('image');
                            if (isImage) {
                                previewUrl = URL.createObjectURL(file);
                            } else {
                                previewUrl = null;
                            }
                        }
                    "
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-md file:border-0
                        file:text-sm file:font-semibold
                        file:bg-primary-50 file:text-primary-700
                        hover:file:bg-primary-100"
                />
                <flux:error name="file" />
            </flux:field>

            <!-- Progress Bar -->
            <div x-show="uploading" class="mt-2">
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-primary-600 h-2.5 rounded-full" x-bind:style="`width: ${progress}%`"></div>
                </div>
                <div class="flex justify-between mt-1">
                    <span class="text-xs text-gray-500" x-text="`${progress}% uploaded`"></span>
                    <button
                        type="button"
                        x-on:click="$wire.cancelUpload('file')"
                        class="text-xs text-red-600 hover:text-red-800"
                    >
                        {{ __('Cancel') }}
                    </button>
                </div>
            </div>

            @if($showCollectionInput)
                <flux:input
                    label="{{ __('Collection (optional)') }}"
                    wire:model="collection"
                    placeholder="e.g., profile-images, documents"
                />
            @endif

            <flux:button
                type="submit"
                variant="primary"
                class="w-full"
                x-bind:disabled="uploading"
            >
                <span x-show="!uploading">{{ __($buttonText) }}</span>
                <span x-show="uploading">{{ __('Uploading...') }}</span>
            </flux:button>
        </div>
    </form>

    <script>
        document.addEventListener('livewire:initialized', () => {
            @this.on('{{ $uploadedEvent }}', (data) => {
                // You can add custom JavaScript here to handle the uploaded event
                console.log('File uploaded:', data);
            });
        });
    </script>
</div>
