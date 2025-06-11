<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\Admin\UserCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserCreatedNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function via_method_returns_database_channel()
    {
        $user = User::factory()->create();
        $performingUser = User::factory()->create();
        $notification = new UserCreatedNotification($user, $performingUser);
        $this->assertEquals(['database'], $notification->via($user));
    }

    /** @test */
    public function to_array_returns_correct_data_structure_and_content()
    {
        $user = User::factory()->create();
        $performingUser = User::factory()->create();
        $notification = new UserCreatedNotification($user, $performingUser);
        $data = $notification->toArray($user);

        $this->assertEquals("A new user has registered.", $data['title']);
        $this->assertEquals("User: {$user->name} ({$user->email})", $data['message']);
        $this->assertEquals(route('admin.users.show', $user), $data['url']);
    }
} 