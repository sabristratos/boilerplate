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
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database']; 
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        $translationKey = 'User <strong>:created_user_name</strong> was created by <strong>:performing_user_name</strong>.';
        $translationParams = [
            'created_user_name' => $this->createdUser->name,
            'performing_user_name' => $this->performingUser->name,
        ];
        
        $translatedMessage = __($translationKey, $translationParams);
        
        Log::debug('[UserCreatedNotification] Translated message type: ' . gettype($translatedMessage));
        Log::debug('[UserCreatedNotification] Translated message content:', is_array($translatedMessage) ? $translatedMessage : [$translatedMessage]);

        $finalMessage = (string) $translatedMessage;
        if (empty(trim($finalMessage))) {
            $finalMessage = 'User created notification (message is missing).'; // Default fallback
        }

        return [
            'created_user_id' => $this->createdUser->id,
            'created_user_name' => $this->createdUser->name,
            'performing_user_id' => $this->performingUser->id,
            'performing_user_name' => $this->performingUser->name,
            'message' => $finalMessage,
            'url' => route('admin.users'),
        ];
    }
} 