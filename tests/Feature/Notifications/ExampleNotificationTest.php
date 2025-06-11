<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\ExampleNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class ExampleNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function via_method_uses_notification_facade_get_channels()
    {
        $user = User::factory()->create();
        $notificationService = $this->mock(NotificationService::class);
        $notificationService->shouldReceive('getChannels')->andReturn(['mail', 'database']);

        $notification = new ExampleNotification('Test message');
        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

    /** @test */
    public function to_mail_returns_correct_mail_message()
    {
        $user = User::factory()->create();
        $message = 'This is a test message.';
        $notification = new ExampleNotification($message, 'View Details', route('admin.dashboard'));
        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Example Notification', $mailMessage->subject);
        $this->assertContains($message, $mailMessage->introLines);
        $this->assertEquals('View Details', $mailMessage->actionText);
        $this->assertEquals(route('admin.dashboard'), $mailMessage->actionUrl);
        $this->assertContains(Lang::get('Thank you for using our application!'), $mailMessage->outroLines);
    }

    /** @test */
    public function to_mail_returns_correct_mail_message_without_action()
    {
        $user = User::factory()->create();
        $message = 'This is a test message without an action.';
        $notification = new ExampleNotification($message);
        $mailMessage = $notification->toMail($user);

        $this->assertEquals('Example Notification', $mailMessage->subject);
        $this->assertContains($message, $mailMessage->introLines);
        $this->assertNull($mailMessage->actionText);
        $this->assertNull($mailMessage->actionUrl);
        $this->assertContains(Lang::get('Thank you for using our application!'), $mailMessage->outroLines);
    }

    /** @test */
    public function to_array_returns_correct_data()
    {
        $user = User::factory()->create();
        $message = 'This is a test message.';
        $notification = new ExampleNotification($message, 'View Details', route('admin.dashboard'));
        $data = $notification->toArray($user);

        $this->assertEquals('Example Notification', $data['title']);
        $this->assertEquals($message, $data['message']);
        $this->assertEquals(route('admin.dashboard'), $data['url']);
    }

    /** @test */
    public function to_array_returns_correct_data_without_action()
    {
        $user = User::factory()->create();
        $message = 'This is a test message without an action.';
        $notification = new ExampleNotification($message);
        $data = $notification->toArray($user);

        $this->assertEquals('Example Notification', $data['title']);
        $this->assertEquals($message, $data['message']);
        $this->assertNull($data['url']);
    }
} 