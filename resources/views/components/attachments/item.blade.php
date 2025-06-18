@props(['attachment', 'showActions' => true])

<div {{ $attributes->merge(['class' => 'flex items-center p-3 sm:p-4 bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700']) }}>
    <div class="flex-shrink-0 h-10 w-10 sm:h-12 sm:w-12 bg-gray-100 dark:bg-zinc-700 rounded flex items-center justify-center">
        @if($attachment->isImage())
            <a href="{{ $attachment->url }}" class="glightbox" data-gallery="attachments-gallery" data-title="{{ $attachment->filename }}">
                <img src="{{ $attachment->url }}?v={{ $attachment->updated_at->timestamp }}" alt="{{ $attachment->filename }}" class="h-full w-full object-cover rounded" loading="lazy">
            </a>
        @else
            <svg class="h-6 w-6 sm:h-8 sm:w-8 text-gray-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        @endif
    </div>

    <div class="ml-3 sm:ml-4 flex-1 min-w-0">
        <p class="text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $attachment->filename }}">
            {{ $attachment->filename }}
        </p>
        <div class="text-xs text-gray-500 dark:text-zinc-400 flex flex-wrap items-center space-x-1 sm:space-x-2">
            <span class="truncate">{{ $attachment->mime_type }}</span>
            <span class="hidden sm:inline">•</span>
            <span class="whitespace-nowrap">{{ \Illuminate\Support\Number::fileSize($attachment->size, precision: 2) }}</span>
            @if($attachment->collection)
                <span class="hidden sm:inline">•</span>
                <flux:badge size="sm" class="whitespace-nowrap mt-0.5 sm:mt-0">{{ $attachment->collection }}</flux:badge>
            @endif
        </div>
    </div>

    @if (isset($actions) && $actions->isNotEmpty())
        <div class="ml-2 sm:ml-4 flex-shrink-0 flex items-center">
            {{ $actions }}
        </div>
    @elseif ($showActions)
        <div class="ml-2 sm:ml-4 flex-shrink-0 flex items-center space-x-2">
            <a href="{{ $attachment->url }}" target="_blank" class="text-primary-600 hover:text-primary-900 text-sm dark:text-primary-400 dark:hover:text-primary-300">
                {{ __('View') }}
            </a>
            <a href="{{ $attachment->url }}" download="{{ $attachment->filename }}" class="text-green-600 hover:text-green-900 text-sm dark:text-green-400 dark:hover:text-green-300">
                {{ __('Download') }}
            </a>
        </div>
    @endif
</div>
