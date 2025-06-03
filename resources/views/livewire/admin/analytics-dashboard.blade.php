<div>
    <flux:heading size="xl">{{ __('Analytics Dashboard') }}</flux:heading>
    <flux:button variant="outline" wire:click="loadAnalyticsData" class="my-4" icon="arrow-path">
        {{ __('Refresh Data') }}
    </flux:button>

    <flux:separator variant="subtle" class="my-8" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <flux:card class="text-center">
            <flux:heading size="md" class="text-zinc-500 dark:text-zinc-400">{{ __('Total Page Views') }}</flux:heading>
            <flux:heading size="2xl" class="mt-1">{{ number_format($totalPageViews) }}</flux:heading>
        </flux:card>
        <flux:card class="text-center">
            <flux:heading size="md" class="text-zinc-500 dark:text-zinc-400">{{ __('Unique Visitors Today') }}</flux:heading>
            <flux:heading size="2xl" class="mt-1">{{ number_format($uniqueVisitorsToday) }}</flux:heading>
        </flux:card>
        <flux:card class="text-center">
            <flux:heading size="md" class="text-zinc-500 dark:text-zinc-400">{{ __('Page Views Today') }}</flux:heading>
            <flux:heading size="2xl" class="mt-1">{{ number_format($pageViewsToday) }}</flux:heading>
        </flux:card>
        <flux:card class="text-center">
            <flux:heading size="md" class="text-zinc-500 dark:text-zinc-400">{{ __('Views (Last 7 Days)') }}</flux:heading>
            <flux:heading size="2xl" class="mt-1">{{ number_format($pageViewsLast7Days) }}</flux:heading>
        </flux:card>
        <flux:card class="text-center lg:col-span-2">
            <flux:heading size="md" class="text-zinc-500 dark:text-zinc-400">{{ __('Views (Last 30 Days)') }}</flux:heading>
            <flux:heading size="2xl" class="mt-1">{{ number_format($pageViewsLast30Days) }}</flux:heading>
        </flux:card>
    </div>

    @if(!empty($pageViewsOverTimeData))
        <div class="mb-8">
            <flux:card>
                <flux:heading size="lg" class="mb-4">{{ __('Page Views (Last 30 Days)') }}</flux:heading>
                <flux:chart :value="$pageViewsOverTimeData" class="h-72">
                    <flux:chart.svg>
                        <flux:chart.line field="views" class="text-primary-500" curve="smooth" />
                        <flux:chart.area field="views" class="text-primary-500/10 dark:text-primary-400/10" curve="smooth" />
                        <flux:chart.axis axis="x" field="date" :format="['month' => 'short', 'day' => 'numeric']">
                            <flux:chart.axis.tick />
                            <flux:chart.axis.line />
                        </flux:chart.axis>
                        <flux:chart.axis axis="y" :tick-count="5">
                            <flux:chart.axis.grid />
                            <flux:chart.axis.tick />
                        </flux:chart.axis>
                        <flux:chart.cursor />
                    </flux:chart.svg>
                    <flux:chart.tooltip>
                        <flux:chart.tooltip.heading field="date" :format="['dateStyle' => 'medium']" />
                        <flux:chart.tooltip.value field="views" label="{{__('Views')}}" />
                    </flux:chart.tooltip>
                </flux:chart>
            </flux:card>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Top Pages (All Time)') }}</flux:heading>
            @if(empty($topPages))
                <x-flux.empty-state icon="document-text" heading="{{__('No page data yet')}}" />
            @else
                <ul class="space-y-2">
                    @foreach($topPages as $page)
                        <li class="flex justify-between items-center text-sm">
                            <span class="truncate dark:text-zinc-300" title="{{ $page['path'] }}">{{ Str::limit($page['path'], 50) }}</span>
                            <flux:badge color="blue">{{ number_format($page['views']) }} {{ __('views') }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Top Referrers (All Time)') }}</flux:heading>
            @if(empty($topReferrers))
                <x-flux.empty-state icon="link" heading="{{__('No referrer data yet')}}" />
            @else
                <ul class="space-y-2">
                    @foreach($topReferrers as $referrer)
                        <li class="flex justify-between items-center text-sm">
                            <a href="{{ $referrer['referrer'] }}" target="_blank" rel="noopener noreferrer" class="truncate text-primary-600 dark:text-primary-400 hover:underline" title="{{ $referrer['referrer'] }}">
                                {{ Str::limit(parse_url($referrer['referrer'], PHP_URL_HOST) ?: $referrer['referrer'], 50) }}
                            </a>
                            <flux:badge color="green">{{ number_format($referrer['views']) }} {{ __('visits') }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Top Browsers') }}</flux:heading>
            @if(empty($topBrowsers))
                <x-flux.empty-state icon="cursor-arrow-rays" heading="{{__('No browser data yet')}}" />
            @else
                <ul class="space-y-2">
                    @foreach($topBrowsers as $browser)
                        <li class="flex justify-between items-center text-sm">
                            <span class="dark:text-zinc-300">{{ $browser['browser_name'] }}</span>
                            <flux:badge color="purple">{{ number_format($browser['views']) }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>

        <flux:card>
            <flux:heading size="lg" class="mb-4">{{ __('Top Platforms (OS)') }}</flux:heading>
            @if(empty($topPlatforms))
                <x-flux.empty-state icon="computer-desktop" heading="{{__('No platform data yet')}}" />
            @else
                <ul class="space-y-2">
                    @foreach($topPlatforms as $platform)
                        <li class="flex justify-between items-center text-sm">
                            <span class="dark:text-zinc-300">{{ $platform['platform_name'] }}</span>
                            <flux:badge color="teal">{{ number_format($platform['views']) }}</flux:badge>
                        </li>
                    @endforeach
                </ul>
            @endif
        </flux:card>
    </div>

</div>
