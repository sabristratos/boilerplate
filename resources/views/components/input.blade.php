@props([
    'name',
    'label',
    'type' => 'text',
    'id' => null,
    'helperText' => null,
    'icon' => null,
])

@php
    $id = $id ?? $name;
    $hasError = $errors->has($name);
    $isPassword = $type === 'password';

    // Base classes for the input field with hover glow effect
    $inputClasses = 'block w-full rounded-lg border-0 py-2.5 shadow-[0_1px_2px_0_rgba(0,0,0,0.04)] dark:shadow-none ring-1 ring-inset sm:text-sm sm:leading-6 transition-all duration-150 hover:shadow-[0_0_0_2px] hover:shadow-accent/20';

    // Adjust padding if an icon is present
    $inputClasses .= $icon ? ' pl-10' : ' px-3';
    $inputClasses .= $isPassword ? ' pr-10' : '';

    // Classes for default, error, and focus states with an offset ring
    $ringClasses = $hasError
        ? 'ring-red-500 dark:ring-red-500/80 text-red-900 placeholder:text-red-300 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900'
        : 'ring-zinc-300 dark:ring-zinc-700 text-zinc-900 dark:text-zinc-50 dark:bg-zinc-800/10 dark:bg-zinc-900 placeholder:text-zinc-400 dark:placeholder:text-zinc-500 focus:ring-accent focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-zinc-900';
@endphp

<div class="w-full">
    {{-- Label --}}
    <label for="{{ $id }}" class="block text-sm font-medium leading-6 text-zinc-900 dark:text-zinc-100">{{ $label }}</label>

    <div
        class="relative mt-2"
        @if($isPassword)
        x-data="{
            visible: false,
            toggleVisibility() {
                this.visible = !this.visible;
                this.$refs.input.type = this.visible ? 'text' : 'password';
            }
        }"
        @endif
    >
        {{-- Leading Icon (now dynamically included) --}}
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <flux:icon :name="$icon" class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
            </div>
        @endif

        {{-- Input Element --}}
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $attributes->merge(['class' => "{$inputClasses} {$ringClasses}"]) }}
            @if($isPassword) x-ref="input" @endif
        >

        {{-- Password Visibility Toggle --}}
        @if ($isPassword)
            <button type="button" x-on:click="toggleVisibility" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <template x-if="!visible">
                    <flux:icon name="eye" class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                </template>
                <template x-if="visible">
                    <flux:icon name="eye-slash" class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                </template>
            </button>
        @endif
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
