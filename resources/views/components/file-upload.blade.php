@props([
    'model',
    'collection',
    'multiple' => false,
    'variant' => 'default',
    'label',
    'helpText' => null,
])

@php
    $modelName = $attributes->wire('model')->value();
    $fileData = data_get($this, $modelName);
    $newFiles = $fileData ? Illuminate\Support\Arr::wrap($fileData) : [];
    $existingAttachments = $model?->getAttachments($collection) ?? collect();
    $hasExistingAttachment = $existingAttachments->isNotEmpty();
    $hasNewFile = !empty($newFiles) && $newFiles[0] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
@endphp

<div
    x-data="{
        dropping: false,
        uploading: false,
        progress: 0,
    }"
    x-on:livewire-upload-start="uploading = true"
    x-on:livewire-upload-finish="uploading = false; progress = 0; @this.call('$refresh')"
    x-on:livewire-upload-error="uploading = false; progress = 0;"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    @if ($variant === 'circular')
        <flux:field :label="$label">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden">
                        @if($hasNewFile)
                             <img src="{{ $newFiles[0]->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($hasExistingAttachment)
                             <img src="{{ $existingAttachments->first()->url }}" class="w-full h-full object-cover">
                        @else
                            <x-heroicon-o-user class="w-12 h-12 text-gray-400" />
                        @endif
                    </div>
                     <label for="{{ $modelName }}" class="absolute -bottom-1 -right-1 z-10 cursor-pointer p-1.5 bg-primary-600 hover:bg-primary-700 rounded-full text-white shadow-md">
                        <x-heroicon-s-pencil class="w-4 h-4" />
                        <input id="{{ $modelName }}" name="{{ $modelName }}" type="file" class="sr-only" {{ $attributes }}>
                    </label>
                </div>
                @if ($hasNewFile)
                    <flux:button
                        wire:click="$removeUpload('{{ $modelName }}', '{{ $newFiles[0]->getFilename() }}')"
                        variant="danger"
                        size="sm"
                    >
                        {{ __('Remove') }}
                    </flux:button>
                @elseif ($hasExistingAttachment)
                    <flux:button
                        wire:click="removeAttachment({{ $existingAttachments->first()->id }}, '{{ $collection }}')"
                        variant="danger"
                        size="sm"
                    >
                        {{ __('Remove') }}
                    </flux:button>
                @endif
            </div>
             <flux:error :name="$modelName" />
        </flux:field>
    @else
        <flux:field :label="$label">
            <div class="mt-1">
                <div
                    @dragover.prevent="dropping = true"
                    @dragleave.prevent="dropping = false"
                    @drop.prevent
                    :class="{ 'border-primary-500 bg-primary-50': dropping, 'border-gray-300 dark:border-zinc-700': !dropping }"
                    class="flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md"
                >
                    <div class="space-y-1 text-center">
                        <x-heroicon-o-document-arrow-up class="mx-auto h-12 w-12 text-gray-400" />
                        <div class="flex text-sm text-gray-600 dark:text-gray-400">
                            <label for="{{ $modelName }}" class="relative cursor-pointer bg-white dark:bg-zinc-900 rounded-md font-medium text-primary-600 dark:text-primary-400 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                <span>{{ __('Upload a file') }}</span>
                                <input id="{{ $modelName }}" name="{{ $modelName }}" type="file" class="sr-only" {{ $attributes }} {{ $multiple ? 'multiple' : '' }}>
                            </label>
                            <p class="pl-1">{{ __('or drag and drop') }}</p>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $helpText ?? __('Any file up to 10MB') }}</p>
                    </div>
                </div>
            </div>
            <flux:error :name="$modelName" />
        </flux:field>
    @endif

    <!-- Progress Bar -->
    <div x-show="uploading" class="mt-2" x-cloak>
        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
            <div class="bg-primary-600 h-2.5 rounded-full" x-bind:style="`width: ${progress}%`"></div>
        </div>
    </div>

    <!-- Previews -->
    <div class="mt-4 space-y-2">
        <!-- New files -->
        @if ($variant === 'default')
            @foreach ($newFiles as $file)
                @if ($file instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                    <div class="bg-gray-50 dark:bg-zinc-800 p-2 rounded-md flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if (Str::startsWith($file->getMimeType(), 'image'))
                                <a href="{{ $file->temporaryUrl() }}" class="glightbox">
                                    <img src="{{ $file->temporaryUrl() }}" class="h-10 w-10 rounded-md object-cover">
                                </a>
                            @else
                                <div class="h-10 w-10 bg-gray-200 dark:bg-zinc-700 rounded-md flex items-center justify-center">
                                    <x-heroicon-o-document class="w-6 h-6 text-gray-500" />
                                </div>
                            @endif
                            <div class="text-sm">
                                <p class="font-medium text-gray-900 dark:text-gray-200">{{ $file->getClientOriginalName() }}</p>
                                <p class="text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Number::fileSize($file->getSize()) }}</p>
                            </div>
                        </div>
                        <button type="button" wire:click="$removeUpload('{{ $modelName }}', '{{ $file->getFilename() }}')" class="text-gray-400 hover:text-gray-500">
                            <x-heroicon-s-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                @endif
            @endforeach
        @endif

        <!-- Existing files -->
        @if ($variant === 'default' && $existingAttachments->isNotEmpty())
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400 pt-2">{{ __('Current Files') }}</div>
            @foreach ($existingAttachments as $attachment)
                 <div class="bg-gray-50 dark:bg-zinc-800 p-2 rounded-md flex items-center justify-between" :key="$attachment->id">
                    <div class="flex items-center gap-3">
                        @if ($attachment->isImage())
                            <a href="{{ $attachment->url }}" class="glightbox">
                                <img src="{{ $attachment->url }}" class="h-10 w-10 rounded-md object-cover">
                            </a>
                        @else
                            <div class="h-10 w-10 bg-gray-200 dark:bg-zinc-700 rounded-md flex items-center justify-center">
                                <x-heroicon-o-document class="w-6 h-6 text-gray-500" />
                            </div>
                        @endif
                        <div class="text-sm">
                            <p class="font-medium text-gray-900 dark:text-gray-200">{{ $attachment->meta['title'] ?? $attachment->filename }}</p>
                            <p class="text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Number::fileSize($attachment->size) }}</p>
                        </div>
                    </div>
                     <button type="button" wire:click="removeAttachment({{ $attachment->id }}, '{{ $collection }}')" class="text-red-500 hover:text-red-700 p-2">
                        <x-heroicon-s-trash class="w-5 h-5" />
                    </button>
                </div>
            @endforeach
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        if (typeof GLightbox !== 'undefined') {
            const lightbox = GLightbox({
                selector: '.glightbox'
            });
        }
    });
</script>
@endpush 