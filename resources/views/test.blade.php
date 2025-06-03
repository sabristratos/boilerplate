<x-frontend-layout>
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-4">Test Page</h1>
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" aria-label="Toggle dark mode" />
        <p class="mb-4">This is a test page to verify that the base layout is working correctly.</p>
        <div class="bg-blue-500 text-white p-4 rounded">
            This element uses Tailwind CSS classes.
        </div>
        <img src="{{ asset('/storage/attachments/user/1/e60dadf7-c12f-4882-81fc-485b151f13a0.webp') }}" />
    </div>
</x-frontend-layout>
