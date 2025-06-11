<div>
    <div class="flex justify-between items-center mb-6">
        <flux:heading size="xl">
            {{ __('Notifications') }}
        </flux:heading>
        <flux:button wire:click="markAllAsRead" icon="check">
            {{ __('Mark all as read') }}
        </flux:button>
    </div>

    <div class="space-y-4">
        @forelse ($notifications as $notification)
            <flux:card class="flex items-start {{ is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                <div class="flex-shrink-0">
                    <flux:icon name="information-circle" class="w-6 h-6 text-zinc-500" />
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-zinc-900 dark:text-white">
                        {{ $notification->data['title'] ?? __('Notification') }}
                    </p>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">
                        @php
                            $message = $notification->data['message'] ?? '';
                            if (is_array($message)) {
                                $message = implode(' ', $message);
                            }
                        @endphp
                        {!! $message !!}
                    </p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-500 mt-1">
                        {{ $notification->created_at->diffForHumans() }}
                    </p>
                </div>
            </flux:card>
        @empty
            <flux:card>
                <x-empty-state icon="bell" heading="{{__('No notifications yet')}}" />
            </flux:card>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div> 