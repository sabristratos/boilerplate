<?php

namespace Tests\Feature\Livewire\Admin\Notifications;

use App\Livewire\Admin\Notifications\NotificationFlyout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationFlyoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function createMockNotification(array $data = [], bool $read = false): DatabaseNotification
    {
        // Using a partial mock for DatabaseNotification as it's quite complex to fully mock.
        // Or, you can create actual notifications in the DB if preferred for feature tests.
        $notification = \Mockery::mock(DatabaseNotification::class)->makePartial();
        $notification->id = Str::uuid()->toString();
        $notification->type = 'App\Notifications\ExampleNotification'; // Example type
        $notification->data = array_merge([
            'message' => 'Test notification message',
            'action_text' => 'View',
            'action_url' => '#',
        ], $data);
        $notification->read_at = $read ? now() : null;
        $notification->created_at = now();

        // Mock methods that would interact with DB or other systems if not using real notifications
        $notification->shouldReceive('markAsRead')->andReturnUsing(function () use ($notification) {
            $notification->read_at = now();
        });
        
        return $notification;
    }

    /** @test */
    public function component_mounts_and_refreshes_unread_count()
    {
        Livewire::test(NotificationFlyout::class)
            ->assertSet('unreadCount', 0)
            ->assertDispatched('unread-notifications-count-updated', count: 0);
    }

    /** @test */
    public function notifications_property_loads_user_notifications()
    {
        $this->user->notify(new \App\Notifications\ExampleNotification('Test'));
        
        Livewire::test(NotificationFlyout::class)
            ->assertCount('notifications', 1);
    }

    /** @test */
    public function notifications_property_returns_empty_collection_if_no_user()
    {
        auth()->logout();

        Livewire::test(NotificationFlyout::class)
            ->assertCount('notifications', 0);
    }

    /** @test */
    public function refresh_unread_count_updates_count_and_dispatches_event()
    {
        $this->user->notify(new \App\Notifications\ExampleNotification('Test'));

        Livewire::test(NotificationFlyout::class)
            ->call('refreshUnreadCount')
            ->assertSet('unreadCount', 1)
            ->assertDispatched('unread-notifications-count-updated', count: 1);
    }

    /** @test */
    public function refresh_notifications_calls_refresh_unread_count()
    {
        Livewire::test(NotificationFlyout::class)
            ->call('refreshNotifications')
            ->assertDispatched('unread-notifications-count-updated');
    }

    /** @test */
    public function toggle_flyout_changes_visibility_and_refreshes_count_when_opening()
    {
        Livewire::test(NotificationFlyout::class)
            ->call('toggleFlyout')
            ->assertSet('showFlyout', true)
            ->assertDispatched('unread-notifications-count-updated')
            ->call('toggleFlyout')
            ->assertSet('showFlyout', false);
    }

    /** @test */
    public function mark_as_read_marks_notification_and_refreshes()
    {
        $notification = $this->user->notify(new \App\Notifications\ExampleNotification('Test'));
        
        Livewire::test(NotificationFlyout::class)
            ->call('markAsRead', $this->user->notifications->first()->id)
            ->assertDispatched('unread-notifications-count-updated');

        $this->assertNotNull($this->user->notifications->first()->read_at);
    }

    /** @test */
    public function mark_as_read_does_nothing_if_notification_not_found()
    {
        Livewire::test(NotificationFlyout::class)
            ->call('markAsRead', 'non-existent-id')
            ->assertNotDispatched('unread-notifications-count-updated');
    }

    /** @test */
    public function mark_all_as_read_marks_all_unread_and_refreshes()
    {
        $this->user->notify(new \App\Notifications\ExampleNotification('Test'));

        Livewire::test(NotificationFlyout::class)
            ->call('markAllAsRead')
            ->assertDispatched('unread-notifications-count-updated');

        $this->assertCount(0, $this->user->unreadNotifications);
    }

    /** @test */
    public function listeners_trigger_correct_methods()
    {
        Livewire::test(NotificationFlyout::class)
            ->dispatch('refresh-notifications')
            ->assertDispatched('unread-notifications-count-updated');
    }

    /** @test */
    public function render_method_returns_correct_view()
    {
        Livewire::test(NotificationFlyout::class)
            ->assertViewIs('livewire.admin.notifications.notification-flyout');
    }
} 