# Notification System

This directory contains notification classes for the application. These notifications use the `Notifications` facade to determine which channels to send notifications through based on the application settings.

## Example Usage

```php
use App\Facades\Notifications;
use App\Notifications\ExampleNotification;

// Create a new notification
$notification = new ExampleNotification(
    'This is an example notification',
    'View Details',
    'https://example.com'
);

// Send to a single user
$user = \App\Models\User::find(1);
Notifications::send($user, $notification);

// Or send to multiple users
$users = \App\Models\User::where('active', true)->get();
Notifications::send($users, $notification);
```

## Notification Settings

The notification system uses the following settings from the settings system:

- `email_notifications`: Whether to send email notifications
- `browser_notifications`: Whether to send browser notifications
- `mobile_push_notifications`: Whether to send mobile push notifications
- `notification_frequency`: How often to send notifications (immediately, hourly, daily, weekly)

These settings can be managed through the admin settings interface.

## Creating New Notifications

To create a new notification:

1. Create a new class that extends `Illuminate\Notifications\Notification`
2. Implement the `via` method to use `Notifications::getChannels()`
3. Implement the necessary channel methods (e.g., `toMail`, `toArray`, etc.)

Example:

```php
use App\Facades\Notifications;
use Illuminate\Notifications\Notification;

class MyNotification extends Notification
{
    public function via($notifiable)
    {
        return Notifications::getChannels();
    }
    
    // Implement other methods as needed
}
```

## Future Improvements

- Implement a queueing system for notifications based on the `notification_frequency` setting
- Add support for more notification channels (SMS, Slack, etc.)
- Create a notification log to track sent notifications
