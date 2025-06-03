@props(['title' => setting('site_name', config('app.name', 'Laravel'))])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', setting('default_language', app()->getLocale())) }}"
    @class([
        'dark' => setting('enable_dark_mode', true) &&
                 (setting('theme', 'light') === 'dark' ||
                 (setting('theme', 'light') === 'system' &&
                  request()->header('Sec-CH-Prefers-Color-Scheme') === 'dark'))
    ])
>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ setting('site_description', 'A TALL Stack Boilerplate') }}">

    <title>{{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />


    @livewireStyles
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic CSS based on settings with cache busting -->
    <link rel="stylesheet" href="{{ route('dynamic.css') }}?v={{ time() }}">

    <!-- Flux Appearance -->
    @php
        $enableDarkMode = setting('enable_dark_mode', true);
        $theme = setting('theme', 'light');
    @endphp

    @if($enableDarkMode)
        @if($theme === 'system')
            @fluxAppearance
        @elseif($theme === 'dark')
            @fluxAppearance(true)
        @else
            @fluxAppearance(false)
        @endif
    @endif
</head>
<body class="antialiased">
    <div class="min-h-screen">
        <!-- Page Content -->
        {{ $slot }}
    </div>

    <!-- Flux Toast Component -->
    @persist('toast')
        <flux:toast position="bottom right" />
    @endpersist

    <!-- Flux Scripts -->
    @livewireScripts
    @fluxScripts
</body>
</html>
