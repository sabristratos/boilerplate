<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"
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
    <meta name="description" content="{{ Str::of(setting('seo_description_template', '{description}'))->replace('{description}', setting('site_description', 'A TALL Stack Boilerplate')) }}">
    <meta name="keywords" content="{{ setting('seo_keywords') }}">

    <title>
        @if (isset($title))
            {{ Str::of(setting('seo_title_template', '{title} - {site_name}'))->replace('{title}', $title)->replace('{site_name}', setting('site_name', config('app.name', 'Laravel'))) }}
        @else
            {{ setting('site_name', config('app.name', 'Laravel')) }}
        @endif
    </title>

    @if(setting('favicon'))
        <link rel="icon" href="{{ setting('favicon') }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    @if(setting('seo_use_sitemap', true))
        <link rel="sitemap" type="application/xml" title="Sitemap" href="/sitemap.xml">
    @endif

    @stack('meta')

    @livewireStyles
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

    {!! setting('custom_header_code') !!}

    @if(setting('google_analytics_id'))
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ setting('google_analytics_id') }}');
    </script>
    @endif

    @if(setting('google_tag_manager_id'))
    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ setting('google_tag_manager_id') }}');</script>
    <!-- End Google Tag Manager -->
    @endif
</head>
<body
    class="antialiased font-sans"
    x-data="{
        init() {
            const toast = @js(session('flux-toast'));
            if (toast) {
                this.$flux.toast(toast);
            }
        }
    }"
    x-init="
        @if(session()->has('impersonation_success'))
            $flux.toast({
                heading: @js(__('Impersonation Started')),
                text: @js(session('impersonation_success')),
                variant: 'success'
            });
        @endif
    "
    :class="$flux.dark ? 'dark' : ''"
>
    @if(setting('google_tag_manager_id'))
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ setting('google_tag_manager_id') }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    @endif

    {!! setting('custom_body_code') !!}

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

    @if(session()->has('impersonator_id'))
        <div class="fixed bottom-0 left-0 w-full bg-yellow-400 dark:bg-yellow-600 text-black dark:text-white p-4 text-center z-50 shadow-lg">
            <livewire:stop-impersonation />
        </div>
    @endif

    @livewire('stop-impersonation')

    @persist('toast')
        <flux:toast />
    @endpersist
</body>
</html> 