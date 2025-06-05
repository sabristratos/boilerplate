<?php

namespace App\Livewire\Admin\Notifications;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\User;

/**
 * @property-read \Illuminate\Support\Collection $notifications
 */
class NotificationFlyout extends Component
{
    public bool $showFlyout = false;
    public int $unreadCount = 0;

    protected $listeners = [
        'notificationReceived' => 'refreshNotifications',
        'markNotificationAsRead' => 'markAsRead',
        'markAllNotificationsAsRead' => 'markAllAsRead',
    ];

    /**
     * Mount the component.
     * Fetches initial unread count.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->refreshUnreadCount();
    }

    /**
     * Fetches the current user's notifications.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getNotificationsProperty(): Collection
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return collect();
        }
        // Fetch, for example, the latest 20 notifications (read or unread)
        $notifications = $user->notifications()->latest()->take(20)->get();

        // Log the data of the first notification if available
        if ($notifications->isNotEmpty()) {
            Log::debug('Notification data structure:', $notifications->first()->data);
        }

        return $notifications;
    }

    /**
     * Refreshes the unread notification count.
     *
     * @return void
     */
    public function refreshUnreadCount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        $this->unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        $this->dispatch('unread-notifications-count-updated', count: $this->unreadCount);
    }

    /**
     * Refreshes both notifications and their unread count.
     * Typically called after a new notification is received.
     *
     * @return void
     */
    public function refreshNotifications(): void
    {
        // This will cause the notifications getter to re-evaluate
        $this->refreshUnreadCount(); // This will also dispatch the event
    }

    /**
     * Toggles the flyout visibility.
     *
     * @return void
     */
    public function toggleFlyout(): void
    {
        $this->showFlyout = !$this->showFlyout;
        if ($this->showFlyout) {
            // Optionally, mark notifications as read when flyout is opened
            // Or, more commonly, mark them as "seen" to stop a badge, but not as fully "read"
            // For now, we'll just show them. Reading will be explicit.
            $this->refreshUnreadCount(); // Ensure count is fresh when opening
        }
    }

    /**
     * Marks a specific notification as read.
     *
     * @param string $notificationId
     * @return void
     */
    public function markAsRead(string $notificationId): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $notification = $user->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->refreshNotifications(); // This will refresh count and dispatch event
            }
        }
    }

    /**
     * Marks all unread notifications for the user as read.
     *
     * @return void
     */
    public function markAllAsRead(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
            $this->refreshNotifications(); // This will refresh count and dispatch event
        }
    }

    /**
     * Renders the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function render(): View
    {
        return view('livewire.admin.notifications.notification-flyout', [
            'notifications' => $this->notifications,
        ]);
    }
} 