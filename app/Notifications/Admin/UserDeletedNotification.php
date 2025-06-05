<?php

namespace App\Notifications\Admin;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return [
            'deleted_user_id' => $this->deletedUserData['id'] ?? null,
            'deleted_user_name' => $this->deletedUserData['name'] ?? __('[Unknown User]'),
            'performing_user_id' => $this->performingUser->id,
            'performing_user_name' => $this->performingUser->name,
            'message' => tap(new HtmlString(__(
                'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.',
                [
                    'deleted_user_name' => $this->deletedUserData['name'] ?? __('[Unknown User]'),
                    'deleted_user_id' => $this->deletedUserData['id'] ?? __('[Unknown ID]'),
                    'performing_user_name' => $this->performingUser->name,
                ]
            )), function ($htmlString) {
                if (empty(trim($htmlString->toHtml()))) {
                    return new HtmlString('User deleted notification (message is missing).'); // Default fallback
                }
                return $htmlString;
            }),
            'url' => route('admin.user-management'),
        ];
    }
} 