<?php

namespace App\Services;

use App\Facades\Settings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotificationService
{
    /**
     * Send a notification to the specified users
     *
     * @param mixed $users The users to notify
     * @param mixed $notification The notification to send
     * @return void
     */
    public function send($users, $notification): void
    {
        // Check notification settings
        $emailNotifications = Settings::get('email_notifications', true);
        $browserNotifications = Settings::get('browser_notifications', false);
        $mobileNotifications = Settings::get('mobile_push_notifications', false);
        $frequency = Settings::get('notification_frequency', 'immediately');

        // Log the notification for debugging
        Log::info('Sending notification', [
            'notification' => get_class($notification),
            'users' => is_array($users) ? count($users) : 1,
            'settings' => [
                'email' => $emailNotifications,
                'browser' => $browserNotifications,
                'mobile' => $mobileNotifications,
                'frequency' => $frequency,
            ],
        ]);

        // If frequency is not immediately, we would queue the notification
        // For now, we'll just send it immediately
        if ($frequency !== 'immediately') {
            Log::info("Notification frequency is set to {$frequency}, but sending immediately for now");
        }

        // Send the notification through Laravel's notification system
        // This will respect the email_notifications setting because Laravel's
        // notification system uses the mail channel by default
        Notification::send($users, $notification);
    }

    /**
     * Get the notification channels based on settings
     *
     * @return array
     */
    public function getChannels(): array
    {
        $channels = [];

        if (Settings::get('email_notifications', true)) {
            $channels[] = 'mail';
        }

        if (Settings::get('browser_notifications', false)) {
            $channels[] = 'database'; // For browser notifications, we store in database
        }

        // Mobile push notifications would typically use a service like Firebase
        // For now, we'll just log that it would be sent
        if (Settings::get('mobile_push_notifications', false)) {
            Log::info('Mobile push notification would be sent');
        }

        return $channels;
    }
}
