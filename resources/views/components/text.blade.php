@props([
    'as' => 'p',
    'size' => 'base',
    'weight' => 'normal',
    'color' => 'default'
])

@php
    // Ensure only allowed tags are used for security and predictability.
    $tag = in_array($as, ['p', 'span', 'div', 'label']) ? $as : 'p';

    // Map size props to Tailwind's text-size classes.
    $sizeClasses = [
        'lg' => 'text-lg',
        'base' => 'text-base leading-7', // Added line-height for better readability on base text
        'sm' => 'text-sm',
        'xs' => 'text-xs',
    ][$size] ?? 'text-base leading-7';

    // Map weight props to font-weight classes.
    $weightClasses = [
        'bold' => 'font-bold',
        'semibold' => 'font-semibold',
        'medium' => 'font-medium',
        'normal' => 'font-normal',
    ][$weight] ?? 'font-normal';

    // Map color props to text color classes, using slightly different shades for nuance.
    $colorClasses = [
        'default' => 'text-zinc-800 dark:text-zinc-200',
        'muted' => 'text-zinc-600 dark:text-zinc-400',
        'subtle' => 'text-zinc-500 dark:text-zinc-500',
    ][$color] ?? 'text-zinc-800 dark:text-zinc-200';

    // Merge all classes and any other attributes.
    $attributes = $attributes->merge(['class' => "{$sizeClasses} {$weightClasses} {$colorClasses}"]);
@endphp

<{{ $tag }} {{ $attributes }}>
{{ $slot }}
</{{ $tag }}>
