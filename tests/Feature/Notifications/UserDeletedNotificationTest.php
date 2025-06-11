<?php

namespace Tests\Feature\Notifications;

use App\Models\User;
use App\Notifications\Admin\UserDeletedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletedNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function via_method_returns_database_channel()
    {
        $performingUser = User::factory()->create();
        $notification = new UserDeletedNotification(['name' => 'Test User', 'email' => 'test@example.com'], $performingUser);
        $this->assertEquals(['database'], $notification->via(null));
    }

    /** @test */
    public function to_array_returns_correct_data_with_known_user_data()
    {
        $performingUser = User::factory()->create();
        $userData = ['name' => 'Test User', 'email' => 'test@example.com'];
        $notification = new UserDeletedNotification($userData, $performingUser);
        $data = $notification->toArray(null);

        $this->assertEquals('User Deleted', $data['title']);
        $this->assertEquals("User: Test User (test@example.com) has been deleted.", $data['message']);
        $this->assertNull($data['url']);
    }

    /** @test */
    public function to_array_handles_missing_deleted_user_data_gracefully()
    {
        $performingUser = User::factory()->create();
        $notification = new UserDeletedNotification([], $performingUser);
        $data = $notification->toArray(null);

        $this->assertEquals('User Deleted', $data['title']);
        $this->assertEquals("User: Unknown (Unknown) has been deleted.", $data['message']);
        $this->assertNull($data['url']);
    }

    /** @test */
    public function to_array_handles_partially_missing_deleted_user_data()
    {
        $performingUser = User::factory()->create();
        $notification = new UserDeletedNotification(['name' => 'Test User'], $performingUser);
        $data = $notification->toArray(null);

        $this->assertEquals('User Deleted', $data['title']);
        $this->assertEquals("User: Test User (Unknown) has been deleted.", $data['message']);
        $this->assertNull($data['url']);
    }
} 