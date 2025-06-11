<div>
    <flux:heading size="xl">{{ __('Admin Hub') }}</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
        <!-- User Management Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">{{ __('User Management') }}</flux:heading>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Manage user accounts, profiles, and permissions.') }}
            </flux:text>
            <div class="space-y-2">
                <flux:button href="{{ route('admin.users.index') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="users" class="mr-2" />
                    {{ __('All Users') }}
                </flux:button>
                <flux:button href="{{ route('admin.roles.index') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="shield-check" class="mr-2" />
                    {{ __('Roles & Permissions') }}
                </flux:button>
            </div>
        </flux:card>

        <!-- Content Management Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">{{ __('Content Management') }}</flux:heading>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Manage website content, pages, and media.') }}
            </flux:text>
            <div class="space-y-2">
                <flux:button href="{{ route('admin.taxonomies.index') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="tag" class="mr-2" />
                    {{ __('Taxonomies') }}
                </flux:button>
                <flux:button href="{{ route('admin.attachments') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="photo" class="mr-2" />
                    {{ __('Media') }}
                </flux:button>
            </div>
        </flux:card>

        <!-- System Settings Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">{{ __('System Settings') }}</flux:heading>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Configure system settings and preferences.') }}
            </flux:text>
            <div class="space-y-2">
                <flux:button href="{{ route('admin.settings') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="cog-6-tooth" class="mr-2" />
                    {{ __('General Settings') }}
                </flux:button>
                <flux:button href="{{ route('admin.activity-logs') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="document-magnifying-glass" class="mr-2" />
                    {{ __('Activity Logs') }}
                </flux:button>
            </div>
        </flux:card>
    </div>

    <flux:button variant="outline" wire:click="loadAnalyticsData" class="my-4" icon="arrow-path">
        {{ __('Refresh Data') }}
    </flux:button>

    <!-- Quick Stats Section -->
    <flux:heading size="lg" class="mb-6 mt-8">{{ __('Quick Stats') }}</flux:heading>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total Users') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 p-3 rounded-full">
                    <flux:icon name="user-group" class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4">
                <flux:button href="{{ route('admin.users.index') }}" variant="outline" class="w-full justify-start">
                    {{ __('Manage Users') }}
                </flux:button>
            </div>
        </div>

        <flux:card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900 mr-4">
                    <flux:icon name="photo" class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Media Files') }}</flux:text>
                    <flux:heading size="lg" class="font-semibold">{{ number_format($totalMediaFiles) }}</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>
    {{-- End Quick Stats --}}

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
                <flux:chart :value="json_encode($pageViewsOverTimeData)" class="h-72">
                    <flux:chart.svg>
                        <flux:chart.line field="views" class="text-[var(--color-accent)]" curve="smooth" />
                        <flux:chart.area field="views" class="text-[var(--color-accent)]/10 dark:text-[var(--color-accent-content)]/10" curve="smooth" />
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
                <x-empty-state icon="document-text" heading="{{__('No page data yet')}}" />
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
                <x-empty-state icon="link" heading="{{__('No referrer data yet')}}" />
            @else
                <ul class="space-y-2">
                    @foreach($topReferrers as $referrerEntry)
                        <li class="flex justify-between items-center text-sm">
                            @php
                                $hostDisplay = Str::limit($referrerEntry['host'], 50);
                                // Attempt to make a clickable link. If host doesn't have a scheme, prepend http.
                                $linkHref = (parse_url($referrerEntry['host'], PHP_URL_SCHEME) ? '' : 'http://') . $referrerEntry['host'];
                                if ($referrerEntry['host'] === 'unknown' || filter_var($linkHref, FILTER_VALIDATE_URL) === false) {
                                    $linkHref = null; // Don't link if it's 'unknown' or not a valid URL structure
                                }
                            @endphp
                            @if($linkHref)
                                <a href="{{ $linkHref }}" target="_blank" rel="noopener noreferrer" class="truncate text-primary-600 dark:text-primary-400 hover:underline" title="{{ $referrerEntry['host'] }}">
                                    {{ $hostDisplay }}
                                </a>
                            @else
                                <span class="truncate dark:text-zinc-300" title="{{ $referrerEntry['host'] }}">{{ $hostDisplay }}</span>
                            @endif
                            <flux:badge color="green">{{ number_format($referrerEntry['views']) }} {{ __('visits') }}</flux:badge>
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
                <x-empty-state icon="cursor-arrow-rays" heading="{{__('No browser data yet')}}" />
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
                <x-empty-state icon="computer-desktop" heading="{{__('No platform data yet')}}" />
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
