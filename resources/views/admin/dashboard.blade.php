<x-admin-layout>
    <flux:heading size="xl">Admin Dashboard</flux:heading>

    <flux:separator variant="subtle" class="my-8" />

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- User Management Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">User Management</flux:heading>
            <flux:text class="text-sm text-gray-600">
                Manage user accounts, profiles, and permissions.
            </flux:text>
            <div class="space-y-2">
                <flux:button href="{{ route('admin.profile') }}" variant="outline" class="w-full justify-start">
                    <flux:icon name="user" class="mr-2" />
                    User Profile
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="users" class="mr-2" />
                    All Users
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="shield-check" class="mr-2" />
                    Roles & Permissions
                </flux:button>
            </div>
        </flux:card>

        <!-- Content Management Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">Content Management</flux:heading>
            <flux:text class="text-sm text-gray-600">
                Manage website content, pages, and media.
            </flux:text>
            <div class="space-y-2">
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="document-text" class="mr-2" />
                    Pages
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="newspaper" class="mr-2" />
                    Posts
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="photo" class="mr-2" />
                    Media
                </flux:button>
            </div>
        </flux:card>

        <!-- System Settings Card -->
        <flux:card class="space-y-4">
            <flux:heading size="md">System Settings</flux:heading>
            <flux:text class="text-sm text-gray-600">
                Configure system settings and preferences.
            </flux:text>
            <div class="space-y-2">
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="cog-6-tooth" class="mr-2" />
                    General Settings
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="paint-brush" class="mr-2" />
                    Appearance
                </flux:button>
                <flux:button href="#" variant="outline" class="w-full justify-start">
                    <flux:icon name="bell" class="mr-2" />
                    Notifications
                </flux:button>
            </div>
        </flux:card>
    </div>

    <flux:separator variant="subtle" class="my-8" />

    <!-- Quick Stats Section -->
    <flux:heading size="lg" class="mb-6">Quick Stats</flux:heading>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <flux:card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 mr-4">
                    <flux:icon name="users" class="h-6 w-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</flux:text>
                    <flux:heading size="lg" class="font-semibold">1,234</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 mr-4">
                    <flux:icon name="document-text" class="h-6 w-6 text-green-600 dark:text-green-400" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pages</flux:text>
                    <flux:heading size="lg" class="font-semibold">56</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900 mr-4">
                    <flux:icon name="newspaper" class="h-6 w-6 text-purple-600 dark:text-purple-400" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Posts</flux:text>
                    <flux:heading size="lg" class="font-semibold">128</flux:heading>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-amber-100 dark:bg-amber-900 mr-4">
                    <flux:icon name="photo" class="h-6 w-6 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <flux:text class="text-sm font-medium text-gray-600 dark:text-gray-400">Media Files</flux:text>
                    <flux:heading size="lg" class="font-semibold">512</flux:heading>
                </div>
            </div>
        </flux:card>
    </div>
</x-admin-layout>
