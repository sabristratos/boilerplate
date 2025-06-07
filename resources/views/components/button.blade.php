@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'pill' => false,
    'squared' => false,
])

@php
    $tag = $href ? 'a' : 'button';

    $baseClasses = 'inline-flex items-center justify-center font-semibold focus:outline-none transition-all duration-300 ease-in-out relative overflow-hidden group disabled:opacity-50 disabled:cursor-not-allowed';

    $sizeClasses = match ($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'lg' => 'px-6 py-3 text-lg',
        'xl' => 'px-7 py-4 text-xl',
        default => 'px-4 py-2 text-base',
    };

    $roundedClasses = 'rounded-md';
    if ($pill) {
        $roundedClasses = 'rounded-full';
    } elseif ($squared) {
        $roundedClasses = 'rounded-none';
    }

    $variantClasses = '';
    $fillEffectColorValue = 'rgba(255,255,255,0.2)';
    $needsFillEffect = false;

    switch ($variant) {
        case 'secondary':
            $variantClasses = 'bg-zinc-600 text-white border border-transparent hover:bg-zinc-700';
            $fillEffectColorValue = 'rgba(255,255,255,0.15)';
            $variantClasses .= ' hover:text-white';
            $needsFillEffect = true;
            break;
        case 'outline':
            $variantClasses = 'bg-transparent text-accent border border-accent hover:text-white';
            $fillEffectColorValue = 'var(--color-accent)';
            $needsFillEffect = true;
            break;
        case 'ghost':
            $variantClasses = 'bg-transparent text-accent hover:bg-accent/10';
            $needsFillEffect = false;
            break;
        case 'link':
            $variantClasses = 'bg-transparent text-accent hover:underline';
            $needsFillEffect = false;
            break;
        default:
            $variantClasses = 'bg-accent text-accent-foreground border border-transparent';
            $fillEffectColorValue = 'rgba(255,255,255,0.3)';
            $variantClasses .= ' hover:text-accent-foreground';
            $needsFillEffect = true;
            break;
    }

    if ($needsFillEffect) {
        $variantClasses .= ' btn-fill-effect';
    }

    $classes = Arr::toCssClasses([
        $baseClasses,
        $sizeClasses,
        $roundedClasses,
        $variantClasses,
    ]);

    $buttonAttributes = $tag === 'button' ? ['type' => $type] : [];
    $linkAttributes = $tag === 'a' ? ['href' => $href] : [];

    $inlineStyle = $needsFillEffect ? "--btn-fill-color: {$fillEffectColorValue};" : '';

@endphp

<{{ $tag }}
    {{ $attributes->merge($buttonAttributes)->merge($linkAttributes)->class($classes) }}
@if($disabled)
    disabled @endif
             @if($needsFillEffect) style="{{ $inlineStyle }}"
    @endif
>
    <span class="relative z-10">{{ $slot }}</span>
</{{ $tag }}>
