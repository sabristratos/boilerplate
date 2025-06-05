<?php

namespace Tests\Unit\Notifications;

use App\Facades\Notifications as NotificationsFacade; // Alias to avoid conflict
use App\Models\User;
use App\Notifications\ExampleNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\App;
use PHPUnit\Framework\TestCase;

class ExampleNotificationTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        $this->user = new User(); // A simple mock user for type hinting
    }

    /** @test */
    public function via_method_uses_notification_facade_get_channels()
    {
        $notification = new ExampleNotification('Test Message');
        $expectedChannels = ['mail', 'database'];

        NotificationsFacade::shouldReceive('getChannels')
            ->once()
            ->andReturn($expectedChannels);

        $this->assertEquals($expectedChannels, $notification->via($this->user));
    }

    /** @test */
    public function to_mail_returns_correct_mail_message()
    {
        $message = 'This is a test message.';
        $actionText = 'Click Here';
        $actionUrl = 'http://example.com/action';

        $notification = new ExampleNotification($message, $actionText, $actionUrl);
        $mailMessage = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Example Notification', $mailMessage->subject);
        $this->assertContains($message, $mailMessage->introLines);
        $this->assertEquals($actionText, $mailMessage->actionText);
        $this->assertEquals($actionUrl, $mailMessage->actionUrl);
        $this->assertContains('Thank you for using our application!', $mailMessage->outroLines);
    }

    /** @test */
    public function to_mail_returns_correct_mail_message_without_action()
    {
        $message = 'This is a test message without action.';
        $notification = new ExampleNotification($message);
        $mailMessage = $notification->toMail($this->user);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);
        $this->assertEquals('Example Notification', $mailMessage->subject);
        $this->assertContains($message, $mailMessage->introLines);
        $this->assertNull($mailMessage->actionText);
        $this->assertNull($mailMessage->actionUrl);
        $this->assertContains('Thank you for using our application!', $mailMessage->outroLines);
    }

    /** @test */
    public function to_array_returns_correct_data()
    {
        $message = 'Another test message.';
        $actionText = 'View Details';
        $actionUrl = 'http://example.com/details';

        $notification = new ExampleNotification($message, $actionText, $actionUrl);
        $arrayData = $notification->toArray($this->user);

        $expectedData = [
            'message' => $message,
            'action_text' => $actionText,
            'action_url' => $actionUrl,
        ];

        $this->assertEquals($expectedData, $arrayData);
    }

    /** @test */
    public function to_array_returns_correct_data_without_action()
    {
        $message = 'Another test message without action.';
        $notification = new ExampleNotification($message);
        $arrayData = $notification->toArray($this->user);

        $expectedData = [
            'message' => $message,
            'action_text' => '',
            'action_url' => '',
        ];

        $this->assertEquals($expectedData, $arrayData);
    }
} 