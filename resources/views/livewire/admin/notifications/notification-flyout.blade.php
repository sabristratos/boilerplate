<div>
    {{-- This main div is required by Livewire for the component root --}}

    <flux:modal name="notification-flyout" variant="flyout" position="right" wire:model.self="showFlyout" class="w-full md:w-96 lg:w-1/3">
        <div class="flex flex-col h-full">
            <div>
                <div class="flex items-center mb-2 justify-between">
                    <flux:heading size="lg">{{ __('Notifications') }}</flux:heading>
                </div>
                @if ($unreadCount > 0)
                    <flux:button wire:click="markAllAsRead" variant="filled" size="sm">
                        {{ __('Mark all as read') }}
                    </flux:button>
                @endif
            </div>

            @if ($notifications->isEmpty())
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <flux:icon name="bell" class="w-12 h-12 text-zinc-400 dark:text-zinc-500 mb-4" />
                    <flux:heading size="md">{{ __('No notifications') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('You currently have no new notifications.') }}</flux:text>
                </div>
            @else
                <div class="flex-1 overflow-y-auto space-y-4">
                    @foreach ($notifications as $notification)
                        <div class="p-4 rounded-lg {{ $notification->read_at ? 'bg-zinc-50 dark:bg-zinc-800/50' : 'bg-white dark:bg-zinc-800 shadow-sm' }}">
                            <div class="flex items-start justify-between">
                                <div class="prose prose-sm dark:prose-invert max-w-none">
                                    {!! is_array($notification->data['message']) ? implode(' ', array_map('strval', $notification->data['message'])) : $notification->data['message'] !!}
                                </div>
                                @if (!$notification->read_at)
                                    <flux:button
                                        wire:click="markAsRead('{{ $notification->id }}')"
                                        variant="ghost"
                                        size="xs"
                                        class="ml-2 -mr-1 -mt-1"
                                        :tooltip="__('Mark as read')"
                                    >
                                        <flux:icon name="check-circle" class="w-5 h-5" />
                                    </flux:button>
                                @endif
                            </div>
                            <div class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 flex items-center justify-between">
                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                @if (isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="hover:underline">
                                        {{ __('View details') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="p-6 border-t border-zinc-200 dark:border-zinc-700 text-center">
                <flux:button variant="outline" wire:click="toggleFlyout">{{ __('Close') }}</flux:button>
                {{-- Maybe a link to an 'All Notifications' page in the future --}}
            </div>
        </div>
    </flux:modal>

    {{-- The bell icon trigger will be placed in the admin layout --}}
    {{-- For now, this component only manages the flyout content --}}
</div>
