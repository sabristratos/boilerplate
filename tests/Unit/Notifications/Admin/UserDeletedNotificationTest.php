<?php

namespace Tests\Unit\Notifications\Admin;

use App\Models\User;
use App\Notifications\Admin\UserDeletedNotification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use PHPUnit\Framework\TestCase;

class UserDeletedNotificationTest extends TestCase
{
    protected array $deletedUserData;
    protected User $performingUser;
    protected User $notifiableUser;

    protected function setUp(): void
    {
        $this->deletedUserData = ['id' => 1, 'name' => 'Deleted User'];
        $this->performingUser = new User(['id' => 2, 'name' => 'Admin User']);
        $this->notifiableUser = new User(); // The user receiving the notification

        URL::shouldReceive('route')
            ->with('admin.user-management', [], true)
            ->andReturn('http://localhost/admin/user-management');

        Lang::shouldReceive('get')->andReturnUsing(function ($key, $replace = [], $locale = null) {
            $string = $key;
            if ($key === 'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.') {
                $deletedUserName = $replace['deleted_user_name'] ?? Lang::get('[Unknown User]');
                $deletedUserId = $replace['deleted_user_id'] ?? Lang::get('[Unknown ID]');
                $string = "User <strong>{$deletedUserName}</strong> (ID: {$deletedUserId}) was deleted by <strong>{$replace['performing_user_name']}</strong>.";
            } elseif ($key === '[Unknown User]') {
                return '[Unknown User]';
            } elseif ($key === '[Unknown ID]') {
                return '[Unknown ID]';
            }
            return $string;
        });
    }

    /** @test */
    public function via_method_returns_database_channel()
    {
        $notification = new UserDeletedNotification($this->deletedUserData, $this->performingUser);
        $this->assertEquals(['database'], $notification->via($this->notifiableUser));
    }

    /** @test */
    public function to_array_returns_correct_data_with_known_user_data()
    {
        $notification = new UserDeletedNotification($this->deletedUserData, $this->performingUser);
        $arrayData = $notification->toArray($this->notifiableUser);

        $this->assertEquals($this->deletedUserData['id'], $arrayData['deleted_user_id']);
        $this->assertEquals($this->deletedUserData['name'], $arrayData['deleted_user_name']);
        $this->assertEquals($this->performingUser->id, $arrayData['performing_user_id']);
        $this->assertEquals($this->performingUser->name, $arrayData['performing_user_name']);

        $expectedMessageString = Lang::get(
            'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.',
            [
                'deleted_user_name' => $this->deletedUserData['name'],
                'deleted_user_id' => $this->deletedUserData['id'],
                'performing_user_name' => $this->performingUser->name,
            ]
        );
        $this->assertEquals(new HtmlString($expectedMessageString), $arrayData['message']);
        $this->assertEquals('http://localhost/admin/user-management', $arrayData['url']);
    }

    /** @test */
    public function to_array_handles_missing_deleted_user_data_gracefully()
    {
        $deletedUserDataMissing = []; // Test with missing id and name
        $notification = new UserDeletedNotification($deletedUserDataMissing, $this->performingUser);
        $arrayData = $notification->toArray($this->notifiableUser);

        $this->assertNull($arrayData['deleted_user_id']);
        $this->assertEquals(Lang::get('[Unknown User]'), $arrayData['deleted_user_name']);
        $this->assertEquals($this->performingUser->id, $arrayData['performing_user_id']);
        $this->assertEquals($this->performingUser->name, $arrayData['performing_user_name']);

        $expectedMessageString = Lang::get(
            'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.',
            [
                'deleted_user_name' => Lang::get('[Unknown User]'),
                'deleted_user_id' => Lang::get('[Unknown ID]'),
                'performing_user_name' => $this->performingUser->name,
            ]
        );
        $this->assertEquals(new HtmlString($expectedMessageString), $arrayData['message']);
        $this->assertEquals('http://localhost/admin/user-management', $arrayData['url']);
    }

     /** @test */
    public function to_array_handles_partially_missing_deleted_user_data()
    {
        $deletedUserDataPartial = ['id' => 5]; // Name is missing
        $notification = new UserDeletedNotification($deletedUserDataPartial, $this->performingUser);
        $arrayData = $notification->toArray($this->notifiableUser);

        $this->assertEquals(5, $arrayData['deleted_user_id']);
        $this->assertEquals(Lang::get('[Unknown User]'), $arrayData['deleted_user_name']);

        $expectedMessageString = Lang::get(
            'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.',
            [
                'deleted_user_name' => Lang::get('[Unknown User]'),
                'deleted_user_id' => 5,
                'performing_user_name' => $this->performingUser->name,
            ]
        );
        $this->assertEquals(new HtmlString($expectedMessageString), $arrayData['message']);

        $deletedUserDataPartialName = ['name' => 'Partial Name']; // ID is missing
        $notification = new UserDeletedNotification($deletedUserDataPartialName, $this->performingUser);
        $arrayData = $notification->toArray($this->notifiableUser);

        $this->assertNull($arrayData['deleted_user_id']);
        $this->assertEquals('Partial Name', $arrayData['deleted_user_name']);

         $expectedMessageString = Lang::get(
            'User <strong>:deleted_user_name</strong> (ID: :deleted_user_id) was deleted by <strong>:performing_user_name</strong>.',
            [
                'deleted_user_name' => 'Partial Name',
                'deleted_user_id' => Lang::get('[Unknown ID]'),
                'performing_user_name' => $this->performingUser->name,
            ]
        );
        $this->assertEquals(new HtmlString($expectedMessageString), $arrayData['message']);
    }
} 