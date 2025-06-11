<x-layouts.base :title="$title ?? null">
    <div class="min-h-screen flex flex-col justify-center bg-zinc-50 dark:bg-zinc-900">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="text-center">
                <a href="/" class="inline-block">
                    <h1 class="text-2xl font-bold text-primary-600 dark:text-primary-500">{{ config('app.name', 'Laravel') }}</h1>
                </a>
            </div>
        </div>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. {{ __('All rights reserved.') }}
        </div>
    </div>
</x-layouts.base> 