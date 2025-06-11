<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Notifications;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class NotificationFlyout extends Component
{
    /** @var \Illuminate\Support\Collection<int, \Illuminate\Notifications\DatabaseNotification> */
    public Collection $notifications;

    public int $unreadCount = 0;

    protected $listeners = [
        'notification-read' => '$refresh',
        'notifications-marked-as-read' => '$refresh',
        'new-notification' => '$refresh',
    ];

    public function mount(): void
    {
        $this->loadNotifications();
    }

    public function loadNotifications(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $this->notifications = $user->notifications;
        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead(string $notificationId): void
    {
        /** @var User $user */
        $user = auth()->user();
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->dispatch('notification-read');
            $this->updateUnreadCount();
        }
        $this->loadNotifications();
    }

    public function markAllAsRead(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->dispatch('notifications-marked-as-read');
        $this->updateUnreadCount();
        $this->loadNotifications();
    }

    public function delete(string $notificationId): void
    {
        Gate::authorize('delete-notifications');
        /** @var User $user */
        $user = auth()->user();
        $notification = $user->notifications()->find($notificationId);
        if ($notification) {
            $notification->delete();
            $this->dispatch('notification-deleted');
            $this->updateUnreadCount();
        }
        $this->loadNotifications();
    }

    public function getListeners(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            ...$this->listeners,
            "echo-private:users.{$user->id},.Illuminate\\Notifications\\Events\\DatabaseNotificationCreated" => 'handleNewNotification',
        ];
    }

    public function handleNewNotification(): void
    {
        $this->loadNotifications();
        $this->updateUnreadCount();
        $this->dispatch('new-notification-received');
    }

    public function updateUnreadCount(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $this->unreadCount = $user->unreadNotifications()->count();
        $this->dispatch('unread-notifications-count-updated', count: $this->unreadCount);
    }

    public function render(): View
    {
        return view('livewire.admin.notifications.notification-flyout');
    }
} 