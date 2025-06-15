<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\User;
use App\Notifications\Admin\UserCreatedNotification;
use App\Notifications\Admin\UserDeletedNotification;
use App\Notifications\User\NewUserWelcomeNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class UserService
{
    public function createUser(array $data, array $selectedRoles): User
    {
        $plainPassword = $data['password'];
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        if (!empty($selectedRoles)) {
            $user->roles()->attach($selectedRoles);
        }

        ActivityLogger::logCreated(
            $user,
            auth()->user(),
            [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $selectedRoles,
                'status' => $user->status->value,
            ],
            'user'
        );

        try {
            $user->notify(new NewUserWelcomeNotification($user, $plainPassword));
        } catch (\Exception $e) {
            Log::error('Failed to send welcome email to user ' . $user->email . ': ' . $e->getMessage());
        }

        try {
            $adminUsers = User::whereHas('roles', fn($q) => $q->where('slug', config('roles.super_admin')))->get();
            if ($adminUsers->isNotEmpty() && auth()->check()) {
                Notification::send($adminUsers, new UserCreatedNotification($user, auth()->user()));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send UserCreatedNotification to admins: ' . $e->getMessage());
        }

        return $user;
    }

    public function updateUser(User $user, array $data, array $selectedRoles): User
    {
        $oldValues = [
            'name' => $user->name,
            'email' => $user->email,
            'roles' => $user->roles->pluck('id')->map(fn ($id) => (string)$id)->toArray(),
            'status' => $user->status->value,
        ];

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $user->roles()->sync($selectedRoles);

        ActivityLogger::logUpdated(
            $user,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $selectedRoles,
                    'status' => $user->status->value,
                ],
            ],
            'user'
        );

        return $user;
    }

    public function deleteUser(User $user): void
    {
        ActivityLogger::logDeleted(
            $user,
            auth()->user(),
            [
                'name' => $user->name,
                'email' => $user->email,
            ],
            'user'
        );

        try {
            $adminUsers = User::whereHas('roles', fn ($q) => $q->where('slug', config('roles.super_admin')))->get();
            if ($adminUsers->isNotEmpty() && auth()->check() && auth()->id() !== $user->id) {
                Notification::send($adminUsers, new UserDeletedNotification($user->toArray(), auth()->user()));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send UserDeletedNotification to admins: ' . $e->getMessage());
        }

        $user->delete();
    }
} 