@props([
    'icon' => null,
    'heading' => null,
    'description' => null,
])

<div {{ $attributes->class('flex flex-col items-center justify-center py-12 px-4 text-center') }}>
    @if ($icon)
        <div class="mb-4 rounded-full bg-gray-100 dark:bg-gray-800 p-3">
            <flux:icon name="{{ $icon }}" class="h-8 w-8 text-gray-500 dark:text-gray-400" />
        </div>
    @endif

    @if ($heading)
        <flux:heading size="lg" class="mt-2">
            {{ $heading }}
        </flux:heading>
    @endif

    @if ($description)
        <flux:text class="mt-2 max-w-md text-gray-500 dark:text-gray-400">
            {{ $description }}
        </flux:text>
    @endif

    @if ($slot->isNotEmpty())
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endif
</div>
