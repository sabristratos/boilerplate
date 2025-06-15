@props([
    'name',
    'label',
    'type' => 'text',
    'id' => null,
    'helperText' => null,
    'icon' => null,
    'iconStyle' => 'o', // 'o' for outline, 's' for solid
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

    <div class="relative mt-2">
        {{-- Leading Icon (now dynamically included) --}}
        @if ($icon)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                @php
                    // Construct the dynamic component tag for the Heroicon
                    $iconComponent = 'heroicon-' . e($iconStyle) . '-' . e($icon);
                @endphp
                <x-dynamic-component :component="$iconComponent" class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
            </div>
        @endif

        {{-- Input Element --}}
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            {{ $attributes->merge(['class' => "{$inputClasses} {$ringClasses}"]) }}
        >

        {{-- Password Visibility Toggle --}}
        @if ($isPassword)
            <div id="toggle-{{ $id }}" class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-3">
                {{-- Eye Icon (Visible by default) --}}
                <x-heroicon-o-eye id="eye-icon-{{ $id }}" class="h-5 w-5 text-zinc-500 dark:text-zinc-400" />
                {{-- Eye Slash Icon (Hidden by default) --}}
                <x-heroicon-o-eye-slash id="eye-slash-icon-{{ $id }}" class="h-5 w-5 text-zinc-500 dark:text-zinc-400 hidden" />
            </div>

            <script>
                // Script is scoped to this specific component instance
                (function() {
                    const passwordInput = document.getElementById('{{ $id }}');
                    const toggle = document.getElementById('toggle-{{ $id }}');
                    const eyeIcon = document.getElementById('eye-icon-{{ $id }}');
                    const eyeSlashIcon = document.getElementById('eye-slash-icon-{{ $id }}');

                    if (passwordInput && toggle && eyeIcon && eyeSlashIcon) {
                        toggle.addEventListener('click', function() {
                            const isPassword = passwordInput.type === 'password';
                            passwordInput.type = isPassword ? 'text' : 'password';
                            eyeIcon.classList.toggle('hidden', isPassword);
                            eyeSlashIcon.classList.toggle('hidden', !isPassword);
                        });
                    }
                })();
            </script>
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
