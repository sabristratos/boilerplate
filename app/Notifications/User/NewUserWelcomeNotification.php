<?php

namespace App\Notifications\User;

use App\Models\User as UserModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable; 

    /**
     * Create a new notification instance.
     *
     * @param UserModel $newUser The user that was created.
     * @param string $generatedPassword The temporarily generated password for the user.
     */
    public function __construct(public UserModel $newUser, public string $generatedPassword)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Welcome to :app_name!', ['app_name' => config('app.name')]))
            ->greeting(__('Hello :user_name,', ['user_name' => $this->newUser->name]))
            ->line(__('An account has been created for you on :app_name.', ['app_name' => config('app.name')]))
            ->line(__('Your login details are:'))
            ->line(__('Email: :user_email', ['user_email' => $this->newUser->email]))
            ->line(__('Password: :password', ['password' => $this->generatedPassword]))
            ->line(__('We strongly recommend changing your password after your first login.'))
            ->action(__('Login to Your Account'), route('login'))
            ->line(__('Thank you for joining us!'));
    }

    /**
     * Get the array representation of the notification.
     *
     * This is useful if you also want to store this notification in the database for the user,
     * though the primary channel is email.
     *
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'message' => __(
                'Welcome! Your account has been created. Login with email :email and the provided password.',
                ['email' => $this->newUser->email]
            ),
        ];
    }
} 