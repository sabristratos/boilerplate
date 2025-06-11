<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class UserDeletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public array $deletedUserData; 
    public User $performingUser;

    /**
     * Create a new notification instance.
     *
     * @param array $deletedUserData Data of the user that was deleted (e.g., ['id' => 1, 'name' => 'John Doe']).
     * @param User $performingUser The user who performed the action.
     */
    public function __construct(array $deletedUserData, User $performingUser)
    {
        $this->deletedUserData = $deletedUserData;
        $this->performingUser = $performingUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $name = $this->deletedUserData['name'] ?? 'Unknown';
        $email = $this->deletedUserData['email'] ?? 'Unknown';

        return [
            'title' => 'User Deleted',
            'message' => "User: {$name} ({$email}) has been deleted.",
            'url' => route('admin.users.index'),
        ];
    }
} 