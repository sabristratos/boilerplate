<?php

namespace App\Livewire\Profile;

use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class NotificationPreferences extends Component
{
    public bool $email_notifications;
    public bool $browser_notifications;
    public bool $mobile_push_notifications;
    public string $notification_frequency;

    public function mount()
    {
        /** @var User $user */
        $user = Auth::user();
        $preferences = $user->notification_preferences ?? [];

        $this->email_notifications = $preferences['email'] ?? true;
        $this->browser_notifications = $preferences['browser'] ?? false;
        $this->mobile_push_notifications = $preferences['mobile_push'] ?? false;
        $this->notification_frequency = $preferences['frequency'] ?? 'immediately';
    }

    public function save()
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notification_preferences = [
            'email' => $this->email_notifications,
            'browser' => $this->browser_notifications,
            'mobile_push' => $this->mobile_push_notifications,
            'frequency' => $this->notification_frequency,
        ];

        $user->save();

        Flux::toast(__('Preferences saved successfully.'));
    }

    public function render()
    {
        return view('livewire.profile.notification-preferences');
    }
}
