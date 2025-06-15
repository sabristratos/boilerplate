@props([
    'level' => 1,
    'variant' => 'title-lg',
    'color' => 'default'
])

@php
    // Determine the HTML tag based on the level prop.
    $tag = 'h' . max(1, min(6, (int) $level));

    // Map variant props to specific Tailwind CSS classes for styling.
    $variantClasses = [
        'display' => 'text-4xl font-extrabold tracking-tight lg:text-5xl',
        'title-xl' => 'text-3xl font-bold tracking-tight',
        'title-lg' => 'text-2xl font-semibold tracking-tight',
        'title-md' => 'text-xl font-semibold tracking-tight',
        'title-sm' => 'text-lg font-medium leading-none',
    ][$variant] ?? 'text-2xl font-semibold tracking-tight';

    // Map color props to text color classes.
    $colorClasses = [
        'default' => 'text-zinc-900 dark:text-zinc-50',
        'muted' => 'text-zinc-500 dark:text-zinc-400',
        'accent' => 'text-accent dark:text-blue-400', // Example accent color
    ][$color] ?? 'text-zinc-900 dark:text-zinc-50';

    // Merge all classes and any other attributes passed to the component.
    $attributes = $attributes->merge(['class' => "{$variantClasses} {$colorClasses}"]);
@endphp

<{{ $tag }} {{ $attributes }}>
{{ $slot }}
</{{ $tag }}>
