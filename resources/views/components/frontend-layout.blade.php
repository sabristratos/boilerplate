<x-app-layout>
    <div class="min-h-screen bg-white dark:bg-zinc-900">
        <!-- Header -->
        <header class="bg-white dark:bg-zinc-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <a href="/" class="flex items-center">
                        <h1 class="text-xl font-bold text-primary-600 dark:text-primary-500">{{ config('app.name', 'Laravel') }}</h1>
                    </a>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary-600 dark:hover:text-primary-500">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary-600 dark:hover:text-primary-500">Log in</a>
                            <a href="{{ route('register') }}" class="text-sm text-zinc-700 dark:text-zinc-300 hover:text-primary-600 dark:hover:text-primary-500">Register</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white dark:bg-zinc-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-zinc-500 dark:text-zinc-400">
                        &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="#" class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-500">Privacy Policy</a>
                        <a href="#" class="text-sm text-zinc-500 dark:text-zinc-400 hover:text-primary-600 dark:hover:text-primary-500">Terms of Service</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</x-app-layout>
