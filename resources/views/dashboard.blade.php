<x-admin-layout>
<div class="min-h-screen bg-zinc-100 dark:bg-zinc-900">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Dashboard</h1>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:button type="submit" variant="subtle">
                        Log Out
                    </flux:button>
                </form>
            </div>

            <flux:card>
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Welcome, {{ Auth::user()->name }}!</h2>
                    <p class="mb-4">You are logged in to your account.</p>

                    <flux:callout title="Account Information" class="mb-4">
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Joined:</strong> {{ Auth::user()->created_at->format('F j, Y') }}</p>
                    </flux:callout>
                </div>
            </flux:card>
        </div>
    </div>
</div>
    </x-frontend-layout>
