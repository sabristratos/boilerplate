<?php

namespace App\Notifications;

use App\Facades\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class ExampleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $message, public ?string $actionText = null, public ?string $actionUrl = null)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Use the NotificationService to get the channels based on settings
        return Notifications::getChannels($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject(Lang::get('Example Notification'))
            ->line($this->message);

        if ($this->actionText && $this->actionUrl) {
            $mailMessage->action($this->actionText, $this->actionUrl);
        }

        $mailMessage->line(Lang::get('Thank you for using our application!'));

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Example Notification',
            'message' => $this->message,
            'url' => $this->actionUrl,
        ];
    }
}
