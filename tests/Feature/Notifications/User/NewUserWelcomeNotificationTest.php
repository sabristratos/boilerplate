<?php

namespace Tests\Feature\Notifications\User;

use App\Models\User;
use App\Notifications\User\NewUserWelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class NewUserWelcomeNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function via_method_returns_mail_channel()
    {
        $user = User::factory()->create();
        $notification = new NewUserWelcomeNotification($user, 'password');
        $this->assertEquals(['mail'], $notification->via(null));
    }

    /** @test */
    public function to_mail_returns_correct_mail_message_structure_and_content()
    {
        $user = User::factory()->create();
        $password = 'password';
        $notification = new NewUserWelcomeNotification($user, $password);
        $mailMessage = $notification->toMail($user);

        $this->assertEquals(Lang::get('Welcome to :app_name!', ['app_name' => config('app.name')]), $mailMessage->subject);
        $this->assertEquals(Lang::get('Hello :name,', ['name' => $user->name]), $mailMessage->greeting);
        $this->assertEquals(Lang::get('Thank you for registering. We are excited to have you on board.'), $mailMessage->introLines[0]);
        $this->assertEquals(Lang::get('Your account has been created. You can login with your email and this temporary password: :password', ['password' => $password]), $mailMessage->introLines[1]);
        $this->assertEquals(Lang::get('Login to your account'), $mailMessage->actionText);
        $this->assertEquals(route('login'), $mailMessage->actionUrl);
        $this->assertEquals(Lang::get("If you did not create an account, no further action is required."), $mailMessage->outroLines[0]);
    }

    /** @test */
    public function to_array_returns_correct_data_structure_and_content()
    {
        $user = User::factory()->create();
        $notification = new NewUserWelcomeNotification($user, 'password');
        $data = $notification->toArray($user);

        $this->assertEquals('Welcome!', $data['title']);
        $this->assertEquals('Thank you for registering.', $data['message']);
        $this->assertEquals(route('login'), $data['url']);
    }
} 