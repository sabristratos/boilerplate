<?php

namespace Tests\Feature\Livewire\Admin\Notifications;

use App\Livewire\Admin\Notifications\NotificationFlyout;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class NotificationFlyoutTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Auth::login($this->user);
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
        $this->user->unreadNotifications()->create([
            'id' => Str::uuid(), 
            'type' => 'App\Notifications\ExampleNotification', 
            'data' => ['message' => 'Unread']
        ]);
        
        Livewire::test(NotificationFlyout::class)
            ->assertSet('unreadCount', 1)
            ->assertDispatched('unread-notifications-count-updated', count: 1);
    }

    /** @test */
    public function notifications_property_loads_user_notifications()
    {
        $notification1 = $this->createMockNotification();
        $notification2 = $this->createMockNotification();

        $this->user = \Mockery::mock($this->user)->makePartial();
        $this->user->shouldReceive('notifications->latest->take->get')
            ->andReturn(collect([$notification1, $notification2]));
        
        Auth::shouldReceive('user')->andReturn($this->user);

        Livewire::test(NotificationFlyout::class)
            ->assertViewHas('notifications', function ($notifications) use ($notification1, $notification2) {
                return $notifications->contains($notification1) && $notifications->contains($notification2);
            });
    }

    /** @test */
    public function notifications_property_returns_empty_collection_if_no_user()
    {
        Auth::logout();
        Auth::shouldReceive('user')->andReturn(null);

        Livewire::test(NotificationFlyout::class)
            ->assertViewHas('notifications', function ($notifications) {
                return $notifications->isEmpty();
            })
            ->assertSet('unreadCount', 0);
    }

    /** @test */
    public function refresh_unread_count_updates_count_and_dispatches_event()
    {
        // Create a real notification for the user
        $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'data' => ['message' => 'Test 1'],
            'read_at' => null,
        ]);
         $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'data' => ['message' => 'Test 2'],
            'read_at' => null,
        ]);

        Livewire::test(NotificationFlyout::class)
            ->call('refreshUnreadCount')
            ->assertSet('unreadCount', 2)
            ->assertDispatched('unread-notifications-count-updated', count: 2);
    }

    /** @test */
    public function refresh_notifications_calls_refresh_unread_count()
    {
        Livewire::test(NotificationFlyout::class)
            ->call('refreshNotifications') // This calls refreshUnreadCount internally
            ->assertSet('unreadCount', 0) // Assuming no notifications initially by default user factory
            ->assertDispatched('unread-notifications-count-updated', count: 0);
    }

    /** @test */
    public function toggle_flyout_changes_visibility_and_refreshes_count_when_opening()
    {
        Livewire::test(NotificationFlyout::class)
            ->assertSet('showFlyout', false)
            ->call('toggleFlyout')
            ->assertSet('showFlyout', true)
            ->assertDispatched('unread-notifications-count-updated') // Refreshed on open
            ->call('toggleFlyout')
            ->assertSet('showFlyout', false);
    }

    /** @test */
    public function mark_as_read_marks_notification_and_refreshes()
    {
        $notification = $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'data' => ['message' => 'Test Unread'],
            'read_at' => null
        ]);
        $this->assertEquals(1, $this->user->unreadNotifications()->count());

        Livewire::test(NotificationFlyout::class)
            ->call('markAsRead', $notification->id)
            ->assertSet('unreadCount', 0)
            ->assertDispatched('unread-notifications-count-updated', count: 0);

        $this->assertNotNull($this->user->notifications()->find($notification->id)->read_at);
    }
    
    /** @test */
    public function mark_as_read_does_nothing_if_notification_not_found()
    {
         $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ExampleNotification',
            'data' => ['message' => 'Test Unread'],
            'read_at' => null
        ]);
        $initialUnreadCount = $this->user->unreadNotifications()->count();

        Livewire::test(NotificationFlyout::class)
            ->call('markAsRead', 'non-existent-id')
            ->assertSet('unreadCount', $initialUnreadCount)
            ->assertDispatched('unread-notifications-count-updated', ['count' => $initialUnreadCount]);
    }

    /** @test */
    public function mark_all_as_read_marks_all_unread_and_refreshes()
    {
        $this->user->notifications()->create([
            'id' => Str::uuid(), 'type' => 'N', 'data' => ['message' => 'Test message 1'], 'read_at' => null
        ]);
        $this->user->notifications()->create([
            'id' => Str::uuid(), 'type' => 'N', 'data' => ['message' => 'Test message 2'], 'read_at' => null
        ]);
        $this->assertEquals(2, $this->user->unreadNotifications()->count());

        Livewire::test(NotificationFlyout::class)
            ->call('markAllAsRead')
            ->assertSet('unreadCount', 0)
            ->assertDispatched('unread-notifications-count-updated', count: 0);

        $this->assertEquals(0, $this->user->unreadNotifications()->count());
    }

    /** @test */
    public function listeners_trigger_correct_methods()
    {
        // For 'notificationReceived' => 'refreshNotifications' which calls 'refreshUnreadCount'
        Livewire::test(NotificationFlyout::class)
            ->emit('notificationReceived')
            ->assertSet('unreadCount', 0) // Assuming no unread initially and refreshUnreadCount is efficient
            ->assertDispatched('unread-notifications-count-updated');

        // For 'markNotificationAsRead' => 'markAsRead'
        $notification = $this->user->notifications()->create([
            'id' => Str::uuid(), 'type' => 'N', 'data' => ['message' => 'Test message for listener'], 'read_at' => null
        ]);
        $this->assertEquals(1, $this->user->unreadNotifications()->count());
        Livewire::test(NotificationFlyout::class)
            ->emit('markNotificationAsRead', $notification->id)
            ->assertSet('unreadCount', 0);
         $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());

        // For 'markAllNotificationsAsRead' => 'markAllAsRead'
        $this->user->notifications()->create([
            'id' => Str::uuid(), 'type' => 'N', 'data' => ['message' => 'Another test message 1'], 'read_at' => null
        ]);
         $this->user->notifications()->create([
            'id' => Str::uuid(), 'type' => 'N', 'data' => ['message' => 'Another test message 2'], 'read_at' => null
        ]);
        $this->assertEquals(2, $this->user->fresh()->unreadNotifications()->count());
        Livewire::test(NotificationFlyout::class)
            ->emit('markAllNotificationsAsRead')
            ->assertSet('unreadCount', 0);
        $this->assertEquals(0, $this->user->fresh()->unreadNotifications()->count());
    }

    /** @test */
    public function render_method_returns_correct_view()
    {
        Livewire::test(NotificationFlyout::class)
            ->assertViewIs('livewire.admin.notifications.notification-flyout');
    }
} 