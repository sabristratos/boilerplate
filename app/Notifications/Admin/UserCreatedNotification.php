<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $createdUser;
    public User $performingUser;

    /**
     * Create a new notification instance.
     *
     * @param User $createdUser The user that was created.
     * @param User $performingUser The user who performed the action.
     */
    public function __construct(User $createdUser, User $performingUser)
    {
        $this->createdUser = $createdUser;
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
        return [
            'title' => 'A new user has registered.',
            'message' => "User: {$this->createdUser->name} ({$this->createdUser->email})",
            'url' => route('admin.users.edit', $this->createdUser),
        ];
    }
} 