@props([
    'name',
    'label',
    'id' => null,
    'helperText' => null,
    'checked' => false,
])

@php
    $id = $id ?? $name;
@endphp

<div class="w-full">
    <div class="flex items-center">
        {{-- The core of the component is a label wrapping the visual parts and the text --}}
        <label for="{{ $id }}" class="relative inline-flex items-center cursor-pointer">
            <input
                type="checkbox"
                name="{{ $name }}"
                id="{{ $id }}"
                class="sr-only peer"
                @if($checked) checked @endif
                {{ $attributes }}
            >
            {{-- The track and knob for the switch --}}
            <div class="w-11 h-6 bg-zinc-200 rounded-full peer dark:bg-zinc-700 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-offset-2 peer-focus:ring-offset-white dark:peer-focus:ring-offset-zinc-900 peer-focus:ring-accent peer-checked:bg-accent after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-white"></div>
            {{-- Text Label --}}
            <span class="ml-3 text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $label }}</span>
        </label>
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
