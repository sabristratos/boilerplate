<?php

namespace App\Services;

use App\Facades\Settings;
use App\Models\NotificationDigest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Notifications\Notification as LaravelNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a notification to the specified users
     *
     * @param \Illuminate\Database\Eloquent\Collection|array|mixed $users The users to notify
     * @param \Illuminate\Notifications\Notification $notification The notification to send
     * @return void
     */
    public function send($users, LaravelNotification $notification): void
    {
        if (!is_a($users, Collection::class)) {
            $users = new Collection([$users]);
        }

        foreach ($users as $user) {
            $frequency = $user->notification_preferences['frequency'] ?? Settings::get('notification_frequency', 'immediately');

            if ($frequency === 'immediately') {
                Notification::send($user, $notification);
                continue;
            }

            if (!method_exists($notification, 'toArray')) {
                Log::warning('Attempted to queue a notification that does not have a toArray method.', [
                    'notification' => get_class($notification),
                ]);
                continue;
            }

            NotificationDigest::create([
                'notifiable_type' => get_class($user),
                'notifiable_id' => $user->getKey(),
                'notification_type' => get_class($notification),
                'data' => $notification->toArray($user),
                'frequency' => $frequency,
            ]);
        }
    }

    /**
     * Get the notification channels based on settings
     *
     * @param \App\Models\User|null $user
     * @return array
     */
    public function getChannels(User $user = null): array
    {
        $channels = [];
        $preferences = $user->notification_preferences ?? [];

        if ($preferences['email'] ?? Settings::get('email_notifications', true)) {
            $channels[] = 'mail';
        }

        if ($preferences['browser'] ?? Settings::get('browser_notifications', false)) {
            $channels[] = 'database';
        }

        if ($preferences['mobile_push'] ?? Settings::get('mobile_push_notifications', false)) {
            Log::info('Mobile push notification would be sent');
        }

        return $channels;
    }
}
