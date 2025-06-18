<div>
    <div class="flex justify-between items-center mb-4">
        <flux:heading size="xl">
            {{ __('attachments.edit_attachment') }}
        </flux:heading>
        <flux:button :href="route('admin.attachments')" variant="outline" icon="arrow-left">
            {{ __('Back to Attachments') }}
        </flux:button>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <form wire:submit.prevent="save">
                <div class="space-y-4">
                    <flux:field>
                        <flux:label for="title">{{ __('Title') }}</flux:label>
                        <flux:input wire:model.defer="title" id="title" />
                    </flux:field>

                    <flux:field>
                        <flux:label for="altText">{{ __('Alt Text') }}</flux:label>
                        <flux:input wire:model.defer="altText" id="altText" />
                    </flux:field>
                </div>

                <div class="mt-6 flex justify-end">
                    <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                </div>
            </form>
        </div>
        <div>
            <flux:card>
                <div class="space-y-2">
                    <flux:heading size="md">{{ __('Attachment Details') }}</flux:heading>
                    @if ($attachment->isImage())
                        <a href="{{ $attachment->url }}" target="_blank">
                            <img src="{{ $attachment->url }}" alt="{{ $attachment->meta['alt_text'] ?? $attachment->filename }}" class="max-w-full h-auto rounded-lg">
                        </a>
                    @else
                        <div class="flex items-center justify-center bg-gray-100 dark:bg-zinc-800 rounded-lg p-8">
                            <flux:icon name="document" class="w-16 h-16 text-gray-400" />
                        </div>
                    @endif
                    <div class="text-sm space-y-1 pt-2">
                        <p><strong>{{ __('Filename') }}:</strong> {{ $attachment->filename }}</p>
                        <p><strong>{{ __('MIME Type') }}:</strong> {{ $attachment->mime_type }}</p>
                        <p><strong>{{ __('Size') }}:</strong> {{ \Illuminate\Support\Number::fileSize($attachment->size) }}</p>
                        <p><strong>{{ __('Uploaded At') }}:</strong> {{ $attachment->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </flux:card>
        </div>
    </div>
</div>
