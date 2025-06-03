<x-app-layout>
    <div class="bg-zinc-50 dark:bg-zinc-900 min-h-screen">
        <flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700 flex flex-col h-full">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            @if(\App\Facades\Settings::get('show_logo_in_header', true))
                <flux:brand href="#" name="{{ \App\Facades\Settings::get('site_name', config('app.name', 'Laravel')) }}" class="px-2" />
            @endif

            <flux:input as="button" variant="filled" placeholder="Search..." icon="magnifying-glass" />

            <flux:navlist variant="outline">
                <flux:navlist.item icon="home" href="{{ route('admin.dashboard') }}" :current="request()->routeIs('admin.dashboard')">Dashboard</flux:navlist.item>
                <flux:navlist.item icon="chart-bar" href="{{ route('admin.analytics') }}" :current="request()->routeIs('admin.analytics')">{{ __('Analytics') }}</flux:navlist.item>

                <flux:navlist.group icon="user" expandable heading="Content" class="grid">
                    <flux:navlist.item href="#" :current="false">Pages</flux:navlist.item>
                    <flux:navlist.item href="#" :current="false">Posts</flux:navlist.item>
                    <flux:navlist.item href="#" :current="false">Media</flux:navlist.item>
                    <flux:navlist.item href="{{ route('admin.attachments') }}" :current="request()->routeIs('admin.attachments')">Attachments</flux:navlist.item>
                    <flux:navlist.item href="{{ route('admin.taxonomies') }}" :current="request()->routeIs('admin.taxonomies')">Taxonomies</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable heading="Users" class="grid">
                    <flux:navlist.item href="{{ route('admin.users') }}" :current="request()->routeIs('admin.users')">All Users</flux:navlist.item>
                    <flux:navlist.item href="{{ route('admin.roles') }}" :current="request()->routeIs('admin.roles')">Roles</flux:navlist.item>
                </flux:navlist.group>

                <flux:navlist.group expandable heading="System" class="grid">
                    <flux:navlist.item href="{{ route('admin.activity-logs') }}" :current="request()->routeIs('admin.activity-logs')">Activity Logs</flux:navlist.item>
                </flux:navlist.group>
            </flux:navlist>

            <flux:spacer />


            <flux:navlist variant="outline">
                <flux:navlist.item icon="cog-6-tooth" href="{{ route('admin.settings') }}" :current="request()->routeIs('admin.settings')">Settings</flux:navlist.item>
                <flux:navlist.item icon="information-circle" href="#" :current="false">Help</flux:navlist.item>
            </flux:navlist>

            <flux:dropdown position="top" align="left" class="max-lg:hidden">
                <flux:profile name="{{ Auth::user()->name ?? 'Admin User' }}" />

                <flux:menu>
                    <flux:menu.item href="{{ route('admin.profile') }}" icon="user">Profile</flux:menu.item>
                    <flux:menu.item href="{{ route('admin.settings') }}" icon="cog-6-tooth">Settings</flux:menu.item>
                    @if(App\Facades\Settings::get('enable_dark_mode', true))
                    <flux:menu.item
                        x-data
                        x-on:click="$flux.dark = !$flux.dark">
                        {{ __('Dark Mode') }}
                        <x-slot:icon>
                            <template x-if="$flux.dark">
                                {{-- Ensure 'variant' matches what flux:icon expects and corresponds to flux:menu.item's styling --}}
                                <flux:icon class="text-zinc-400 mr-2" icon="sun" variant="mini" />
                            </template>
                            <template x-if="!$flux.dark">
                                <flux:icon class="text-zinc-400 mr-2" icon="moon" variant="mini" />
                            </template>
                        </x-slot:icon>
                    </flux:menu.item>
                    @endif
                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown align="right">
                <flux:profile class="-mr-4" />

                <flux:menu>
                    <flux:menu.item href="{{ route('admin.profile') }}" icon="user">Profile</flux:menu.item>
                    <flux:menu.item href="#" icon="cog-6-tooth">Settings</flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">Logout</flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main container class="max-w-7xl">
            {{ $slot }}
        </flux:main>
    </div>
</x-app-layout>
