<?php

namespace Tests\Unit\Notifications\Admin;

use App\Models\User;
use App\Notifications\Admin\UserCreatedNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Lang;

class UserCreatedNotificationTest extends TestCase
{
    protected User $createdUser;
    protected User $performingUser;
    protected User $notifiableUser;

    protected function setUp(): void
    {
        // It's good practice to use factories for model creation in tests
        // but for simplicity in this unit test, we can new them up if they don't hit DB.
        // However, User model might have global scopes or other things if not using factories.
        // For robustness, factories are preferred even in unit tests if models are complex.

        // Create mock users. In a real application, you'd use factories.
        $this->createdUser = new User(['id' => 1, 'name' => 'New User']);
        $this->performingUser = new User(['id' => 2, 'name' => 'Admin User']);
        $this->notifiableUser = new User(); // The user receiving the notification

        // Mock the URL facade for the route() helper
        URL::shouldReceive('route')
            ->with('admin.user-management', [], true) // Ensure all parameters match
            ->andReturn('http://localhost/admin/user-management');

        // Mock the Lang facade for the __() helper function
        Lang::shouldReceive('get')->andReturnUsing(function ($key, $replace = []) {
            $string = $key; // In a real test, you might load actual lang files or provide specific mocks
            if (str_starts_with($key, 'User <strong>:created_user_name</strong> was created by <strong>:performing_user_name</strong>.')) {
                 // Simplified replacement for this specific test case
                $string = 'User <strong>'.$replace['created_user_name'].'</strong> was created by <strong>'.$replace['performing_user_name'].'</strong>.';
            }
            // Add more specific mocks if other translations are used
            return $string;
        });
    }

    /** @test */
    public function via_method_returns_database_channel()
    {
        $notification = new UserCreatedNotification($this->createdUser, $this->performingUser);
        $this->assertEquals(['database'], $notification->via($this->notifiableUser));
    }

    /** @test */
    public function to_array_returns_correct_data_structure_and_content()
    {
        $notification = new UserCreatedNotification($this->createdUser, $this->performingUser);
        $arrayData = $notification->toArray($this->notifiableUser);

        $this->assertEquals($this->createdUser->id, $arrayData['created_user_id']);
        $this->assertEquals($this->createdUser->name, $arrayData['created_user_name']);
        $this->assertEquals($this->performingUser->id, $arrayData['performing_user_id']);
        $this->assertEquals($this->performingUser->name, $arrayData['performing_user_name']);
        
        // Construct the expected message string using the mocked __() behavior
        $expectedMessageString = Lang::get(
            'User <strong>:created_user_name</strong> was created by <strong>:performing_user_name</strong>.',
            [
                'created_user_name' => $this->createdUser->name,
                'performing_user_name' => $this->performingUser->name,
            ]
        );
        $this->assertEquals(new HtmlString($expectedMessageString), $arrayData['message']);
        $this->assertEquals('http://localhost/admin/user-management', $arrayData['url']);
    }
} 