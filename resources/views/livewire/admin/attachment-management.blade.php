<div>
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <flux:heading size="xl">{{ __('Attachments') }}</flux:heading>
            <flux:text class="mt-2">{{ __('Manage all attachments') }}</flux:text>
        </div>
        <flux:button :href="route('admin.attachments.upload')" variant="primary">
            {{ __('attachments.upload') }}
        </flux:button>
    </div>

    <div class="mt-6 flex flex-wrap items-end gap-4">
        <div class="flex-grow md:flex-grow-0 md:w-80">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ __('Search attachments...') }}" />
        </div>
        <div class="flex-grow md:flex-grow-0">
            <flux:select wire:model.live.debounce.150ms="perPage">
                <option value="12">12 {{ __('per page') }}</option>
                <option value="24">24 {{ __('per page') }}</option>
                <option value="48">48 {{ __('per page') }}</option>
            </flux:select>
        </div>
    </div>

    <div class="mt-6">
    @if($attachments->isEmpty())
        <x-empty-state :title="__('No attachments found')" :text="__('Upload your first attachment to get started.')">
             <flux:button :href="route('admin.attachments.upload')" variant="primary">
                {{ __('Upload Attachment') }}
            </flux:button>
        </x-empty-state>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            @foreach ($attachments as $attachment)
                <flux:card class="group" :key="$attachment->id">
                    <a href="{{ route('admin.attachments.edit', $attachment) }}" class="block">
                        @if ($attachment->isImage())
                            <img src="{{ $attachment->url }}" alt="{{ $attachment->meta['alt_text'] ?? $attachment->filename }}"
                                 class="w-full h-32 object-cover">
                        @else
                            <div class="w-full h-32 flex items-center justify-center bg-gray-100 dark:bg-zinc-800">
                                <flux:icon name="document" class="w-12 h-12 text-gray-400" />
                            </div>
                        @endif
                    </a>
                    <div class="p-2 text-sm">
                        <p class="font-semibold truncate" title="{{ $attachment->meta['title'] ?? $attachment->filename }}">
                            {{ $attachment->meta['title'] ?? $attachment->filename }}
                        </p>
                        <p class="text-gray-500">{{ \Illuminate\Support\Number::fileSize($attachment->size) }}</p>
                    </div>
                    <div class="p-2 pt-0 flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <flux:button size="sm" variant="ghost" :href="route('admin.attachments.edit', $attachment)" icon="pencil" />
                        <flux:button size="sm" variant="danger"
                                     wire:click="delete({{ $attachment->id }})"
                                     wire:confirm="{{ __('Are you sure you want to delete this attachment? This action cannot be undone.') }}"
                                     icon="trash"
                        />
                    </div>
                </flux:card>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $attachments->links() }}
        </div>
    @endif
    </div>
</div>
