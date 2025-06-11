<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
class NotificationManagement extends Component
{
    use WithPagination;

    public function markAllAsRead(): void
    {
        /** @var User $user */
        $user = auth()->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        $this->dispatch('notifications-marked-as-read');
    }

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(10);

        return view('livewire.admin.notifications.index', [
            'notifications' => $notifications,
        ]);
    }
} 