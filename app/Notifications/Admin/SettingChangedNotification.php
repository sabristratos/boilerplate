<?php

namespace App\Notifications\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SettingChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Setting $setting,
        public User $causer,
        public mixed $oldValue,
        public mixed $newValue
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Setting Updated: :name', ['name' => $this->setting->display_name]),
            'body' => __(':causer_name has updated the :setting_name setting.', [
                'causer_name' => $this->causer->name,
                'setting_name' => $this->setting->display_name,
            ]),
            'icon' => 'cog-6-tooth',
            'action' => [
                'label' => __('View Settings'),
                'url' => route('admin.settings-management'),
            ],
        ];
    }
}
