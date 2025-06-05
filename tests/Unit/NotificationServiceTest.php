<?php

namespace Tests\\Unit;

use App\\Facades\\Notifications;
use App\\Facades\\Settings;
use App\\Models\\User;
use App\\Notifications\\ExampleNotification;
use App\\Services\\NotificationService;
use Illuminate\\Support\\Facades\\Log;
use Illuminate\\Support\\Facades\\Notification;
use Tests\\TestCase;

class NotificationServiceTest extends TestCase
{
    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = new NotificationService();

        // Mock the Notification facade
        Notification::fake();

        // Mock the Log facade
        Log::spy();

        // Mock the Settings facade
        // Settings::shouldReceive('get')->with('notifications_send_to_log', false)->andReturn(false);
        // Settings::shouldReceive('get')->with('notifications_default_channels', ['mail'])->andReturn(['mail']);
        // No, we should let the actual config be used for unit testing service logic, or mock per test.
    }

    /** @test */
    public function it_sends_notification_immediately()
    {
        $user = User::factory()->create();
        $notification = new ExampleNotification('Test message', 'Action Text', 'http://example.com');

        $this->notificationService->send($user, $notification);

        Notification::assertSentTo($user, ExampleNotification::class);
        Log::shouldHaveReceived('info')->with('Sending notification', \Illuminate\Support\Facades\Log::toMatchArray([
            'notification' => get_class($notification),
            'users' => 1,
        ]));
    }

    /** @test */
    public function it_sends_notification_to_multiple_users()
    {
        $users = User::factory()->count(3)->create();
        $notification = new ExampleNotification('Test message', 'Action Text', 'http://example.com');

        $this->notificationService->send($users, $notification);

        Notification::assertSentTo($users, ExampleNotification::class);
        Log::shouldHaveReceived('info')->with('Sending notification', \Illuminate\Support\Facades\Log::toMatchArray([
            'notification' => get_class($notification),
            'users' => 3,
        ]));
    }

    /** @test */
    public function it_logs_notification_frequency_if_not_immediate()
    {
        Settings::shouldReceive('get')->with('notification_frequency', 'immediately')->andReturn('daily');
        // Ensure other settings are still mocked
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(true);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(false);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(false);


        $user = User::factory()->create();
        $notification = new ExampleNotification('Test message');

        $this->notificationService->send($user, $notification);

        Log::shouldHaveReceived('info')->with("Notification frequency is set to daily, but sending immediately for now");
        Notification::assertSentTo($user, ExampleNotification::class);
    }

    /** @test */
    public function it_gets_mail_channel_when_email_notifications_are_enabled()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(true);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(false);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(false);

        $channels = $this->notificationService->getChannels();

        $this->assertContains('mail', $channels);
    }

    /** @test */
    public function it_gets_database_channel_when_browser_notifications_are_enabled()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(false);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(true);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(false);

        $channels = $this->notificationService->getChannels();

        $this->assertContains('database', $channels);
        $this->assertNotContains('mail', $channels);
    }

    /** @test */
    public function it_logs_when_mobile_push_notifications_are_enabled_in_get_channels()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(false);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(false);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(true);

        $this->notificationService->getChannels();

        Log::shouldHaveReceived('info')->with('Mobile push notification would be sent');
    }

    /** @test */
    public function it_gets_all_channels_when_all_are_enabled()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(true);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(true);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(true); // This will log

        $channels = $this->notificationService->getChannels();

        $this->assertContains('mail', $channels);
        $this->assertContains('database', $channels);
        Log::shouldHaveReceived('info')->with('Mobile push notification would be sent');
    }

    /** @test */
    public function it_returns_empty_array_when_no_channels_are_enabled()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(false);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(false);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(false);

        $channels = $this->notificationService->getChannels();

        $this->assertEmpty($channels);
    }

    /** @test */
    public function send_method_logs_correct_settings_values()
    {
        Settings::shouldReceive('get')->with('email_notifications', true)->andReturn(true);
        Settings::shouldReceive('get')->with('browser_notifications', false)->andReturn(true);
        Settings::shouldReceive('get')->with('mobile_push_notifications', false)->andReturn(true);
        Settings::shouldReceive('get')->with('notification_frequency', 'immediately')->andReturn('hourly');

        $user = User::factory()->create();
        $notification = new ExampleNotification('Test message');

        $this->notificationService->send($user, $notification);

        Log::shouldHaveReceived('info')->with('Sending notification', \Illuminate\Support\Facades\Log::toMatchArray([
            'settings' => [
                'email' => true,
                'browser' => true,
                'mobile' => true,
                'frequency' => 'hourly',
            ],
        ]));
        Log::shouldHaveReceived('info')->with("Notification frequency is set to hourly, but sending immediately for now");
        Notification::assertSentTo($user, ExampleNotification::class);
    }
} 