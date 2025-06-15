@props([
    'name',
    'label',
    'id' => null,
    'helperText' => null,
    'rows' => 4,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);

    // Base classes for the textarea with hover glow effect
    $textareaClasses = 'block w-full rounded-lg border-0 px-3 py-2.5 shadow-[0_1px_2px_0_rgba(0,0,0,0.04)] dark:shadow-none ring-1 ring-inset sm:text-sm sm:leading-6 transition-all duration-150 hover:shadow-[0_0_0_2px] hover:shadow-accent/20';

    // Classes for default, error, and focus states with an offset ring
    $ringClasses = $hasError
        ? 'ring-red-500 dark:ring-red-500/80 text-red-900 placeholder:text-red-300 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900'
        : 'ring-zinc-300 dark:ring-zinc-700 text-zinc-900 dark:text-zinc-50 dark:bg-zinc-800/10 dark:bg-zinc-900 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:ring-accent focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900';
@endphp

<div class="w-full">
    {{-- Label --}}
    <label for="{{ $id }}" class="block text-sm font-medium leading-6 text-zinc-900 dark:text-zinc-100">{{ $label }}</label>

    <div class="relative mt-2">
        {{-- Textarea Element --}}
        <textarea
            name="{{ $name }}"
            id="{{ $id }}"
            rows="{{ $rows }}"
            {{ $attributes->merge(['class' => "{$textareaClasses} {$ringClasses}"]) }}
        >{{ $slot }}</textarea>
    </div>

    {{-- Helper Text --}}
    @if ($helperText)
        <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $helperText }}</p>
    @endif

    {{-- Validation Error --}}
    @error($name)
    <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
    @enderror
</div>
