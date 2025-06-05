<?php

namespace Tests\Unit\Notifications\User;

use App\Models\User as UserModel;
use App\Notifications\User\NewUserWelcomeNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\TestCase;

class NewUserWelcomeNotificationTest extends TestCase
{
    protected UserModel $newUser;
    protected string $password;
    protected UserModel $notifiableUser; // Though in this case, notifiable is the newUser

    protected function setUp(): void
    {
        $this->newUser = new UserModel(['id' => 1, 'name' => 'Test User', 'email' => 'test@example.com']);
        $this->password = 'secret123';
        $this->notifiableUser = $this->newUser; // Notification is sent to the new user

        // Mock config facade for app.name
        Config::shouldReceive('get')->with('app.name')->andReturn('My Application');
        Config::shouldReceive('get')->passthru(); // Allow other config calls

        // Mock URL facade for route() helper
        URL::shouldReceive('route')->with('login', [], true)->andReturn('http://localhost/login');

        // Mock Lang facade for __() helper
        Lang::shouldReceive('get')->andReturnUsing(function ($key, $replace = [], $locale = null) {
            $translations = [
                'Welcome to :app_name!' => 'Welcome to '.$replace['app_name'].'!',
                'Hello :user_name,' => 'Hello '.$replace['user_name'].',',
                'An account has been created for you on :app_name.' => 'An account has been created for you on '.$replace['app_name'].'.',
                'Your login details are:' => 'Your login details are:',
                'Email: :user_email' => 'Email: '.$replace['user_email'],
                'Password: :password' => 'Password: '.$replace['password'],
                'We strongly recommend changing your password after your first login.' => 'We strongly recommend changing your password after your first login.',
                'Login to Your Account' => 'Login to Your Account',
                'Thank you for joining us!' => 'Thank you for joining us!',
                'Welcome! Your account has been created. Login with email :email and the provided password.' => 'Welcome! Your account has been created. Login with email '.$replace['email'].' and the provided password.',
            ];
            return $translations[$key] ?? $key;
        });
    }

    /** @test */
    public function via_method_returns_mail_channel()
    {
        $notification = new NewUserWelcomeNotification($this->newUser, $this->password);
        $this->assertEquals(['mail'], $notification->via($this->notifiableUser));
    }

    /** @test */
    public function to_mail_returns_correct_mail_message_structure_and_content()
    {
        $notification = new NewUserWelcomeNotification($this->newUser, $this->password);
        $mailMessage = $notification->toMail($this->notifiableUser);

        $this->assertInstanceOf(MailMessage::class, $mailMessage);

        // Subject
        $expectedSubject = Lang::get('Welcome to :app_name!', ['app_name' => Config::get('app.name')]);
        $this->assertEquals($expectedSubject, $mailMessage->subject);

        // Greeting
        $expectedGreeting = Lang::get('Hello :user_name,', ['user_name' => $this->newUser->name]);
        $this->assertEquals($expectedGreeting, $mailMessage->greeting);

        // Intro Lines
        $this->assertContains(Lang::get('An account has been created for you on :app_name.', ['app_name' => Config::get('app.name')]), $mailMessage->introLines);
        $this->assertContains(Lang::get('Your login details are:'), $mailMessage->introLines);
        $this->assertContains(Lang::get('Email: :user_email', ['user_email' => $this->newUser->email]), $mailMessage->introLines);
        $this->assertContains(Lang::get('Password: :password', ['password' => $this->password]), $mailMessage->introLines);
        $this->assertContains(Lang::get('We strongly recommend changing your password after your first login.'), $mailMessage->introLines);

        // Action
        $this->assertEquals(Lang::get('Login to Your Account'), $mailMessage->actionText);
        $this->assertEquals(URL::route('login', [], true), $mailMessage->actionUrl);

        // Outro Lines
        $this->assertContains(Lang::get('Thank you for joining us!'), $mailMessage->outroLines);
    }

    /** @test */
    public function to_array_returns_correct_data_structure_and_content()
    {
        $notification = new NewUserWelcomeNotification($this->newUser, $this->password);
        $arrayData = $notification->toArray($this->notifiableUser);

        $expectedData = [
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'message' => Lang::get(
                'Welcome! Your account has been created. Login with email :email and the provided password.',
                ['email' => $this->newUser->email]
            ),
        ];

        $this->assertEquals($expectedData, $arrayData);
    }
} 