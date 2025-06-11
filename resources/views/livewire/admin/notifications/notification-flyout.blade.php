<flux:modal name="notification-flyout" variant="flyout">
    <div class="py-1">
        <div class="px-4 py-2 border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ __('Notifications') }}</h3>
            @if ($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                    {{ __('Mark all as read') }}
                </button>
            @endif
        </div>
        <div class="max-h-96 overflow-y-auto">
            @forelse ($notifications as $notification)
                <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 flex items-start {{ is_null($notification->read_at) ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex-shrink-0">
                        {{-- Icon can be dynamic based on notification type --}}
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
                    <div class="ml-2 flex-shrink-0 flex items-center">
                        @if (is_null($notification->read_at))
                            <button wire:click="markAsRead('{{ $notification->id }}')" title="{{ __('Mark as read') }}" class="text-zinc-400 hover:text-primary-500">
                                <span class="sr-only">{{ __('Mark as read') }}</span>
                                <flux:icon name="check-circle" class="w-5 h-5" />
                            </button>
                        @endif
                        @can('delete-notifications')
                        <button wire:click="delete('{{ $notification->id }}')" title="{{ __('Delete') }}" class="ml-2 text-zinc-400 hover:text-red-500">
                            <span class="sr-only">{{ __('Delete') }}</span>
                            <flux:icon name="x-mark" class="w-5 h-5" />
                        </button>
                        @endcan
                    </div>
                </div>
            @empty
                <div class="px-4 py-4 text-center text-zinc-500 dark:text-zinc-400">
                    {{ __('No new notifications') }}
                </div>
            @endforelse
        </div>
        <div class="px-4 py-2 border-t border-zinc-200 dark:border-zinc-700">
            <a href="{{ route('admin.notifications') }}" class="text-sm text-primary-600 dark:text-primary-400 hover:underline w-full text-center block">
                {{ __('View all notifications') }}
            </a>
        </div>
    </div>
</flux:modal>
