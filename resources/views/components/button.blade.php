@props([
    'variant' => 'accent',
    'href' => null
])

@php
    $baseClasses = 'gsap-button min-h-[50px] inline-flex items-center justify-center whitespace-nowrap rounded-[10px] text-sm font-medium transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 gap-2.5 pr-[19px] pl-4 py-2';

    $variantClasses = [
        'accent' => 'bg-accent text-zinc-950 hover:bg-accent/90 shadow-sm',
        'neutral' => 'bg-zinc-800 text-zinc-50 hover:bg-zinc-800/90 shadow-sm',
        'outline' => 'border border-zinc-300 bg-transparent hover:bg-zinc-100 hover:text-zinc-900 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:hover:text-zinc-50',
        'ghost' => 'hover:bg-zinc-100 hover:text-zinc-900 dark:hover:bg-zinc-800 dark:hover:text-zinc-50',
        'subtle' => 'bg-zinc-100 text-zinc-900 hover:bg-zinc-200/80 dark:bg-zinc-800 dark:text-zinc-50 dark:hover:bg-zinc-700/80',
    ][$variant];

    $attributes = $attributes->merge(['class' => "{$baseClasses} {$variantClasses}"]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes }}>
        @if (isset($icon))
            <span class="inline-flex items-center">{{ $icon }}</span>
        @endif

        {{ $slot }}

        @if (isset($trailing))
            <span class="inline-flex items-center">{{ $trailing }}</span>
        @endif
    </a>
@else
    <button {{ $attributes }}>
        @if (isset($icon))
            <span class="inline-flex items-center">{{ $icon }}</span>
        @endif

        {{ $slot }}

        @if (isset($trailing))
            <span class="inline-flex items-center">{{ $trailing }}</span>
        @endif
    </button>
@endif
