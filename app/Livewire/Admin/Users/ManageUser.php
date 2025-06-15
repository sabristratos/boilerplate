<?php

declare(strict_types=1);

namespace App\Livewire\Admin\Users;

use App\Enums\UserStatus;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class ManageUser extends Component
{
    public ?User $user = null;

    public string $name = '';
    public string $email = '';
    public string $status = 'active';
    public string $password = '';
    public string $password_confirmation = '';
    public array $selectedRoles = [];

    public function mount(?User $user): void
    {
        $this->user = $user;
        if ($this->user?->exists) {
            $this->authorize('update', $this->user);
            $this->name = $this->user->name;
            $this->email = $this->user->email;
            $this->status = $this->user->status->value;
            $this->selectedRoles = $this->user->roles->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        } else {
            $this->authorize('create', User::class);
        }
    }

    protected function rules(): array
    {
        $emailRules = ['required', 'email', 'max:255'];
        $isCreating = !$this->user?->exists;

        if ($isCreating) {
            $emailRules[] = 'unique:users,email';
        } else {
            $emailRules[] = Rule::unique('users', 'email')->ignore($this->user->id);
        }

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRules,
            'status' => ['required', Rule::in(array_column(UserStatus::cases(), 'value'))],
            'password' => $isCreating || !empty($this->password) ? 'required|min:8|confirmed' : 'nullable|min:8|confirmed',
            'password_confirmation' => $isCreating || !empty($this->password) ? 'required' : 'nullable',
            'selectedRoles' => 'array',
        ];
    }

    public function save(UserService $userService): void
    {
        if ($this->user?->exists) {
            $this->authorize('update', $this->user);
        } else {
            $this->authorize('create', User::class);
        }

        // Authorize role assignment separately
        if (collect($this->selectedRoles)->diff($this->user?->roles->pluck('id')->map(fn($id) => (string) $id))->isNotEmpty()) {
            $this->authorize('assignRoles', User::class);
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
        ];

        if (!empty($this->password)) {
            $data['password'] = $this->password;
        }

        try {
            if ($this->user?->exists) {
                $userService->updateUser($this->user, $data, $this->selectedRoles);
                Flux::toast(
                    text: __('User updated successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
            } else {
                $user = $userService->createUser($data, $this->selectedRoles);
                Flux::toast(
                    text: __('User created successfully.'),
                    heading: __('Success'),
                    variant: 'success'
                );
                $this->redirect(route('admin.users.edit', $user), navigate: true);
                return;
            }
        } catch (\Exception $e) {
            Log::error('Failed to save user: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to save user. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
            return;
        }

        $this->dispatch('user-saved');
        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.admin.users.manage-user', [
            'roles' => Role::all(),
            'statuses' => UserStatus::cases(),
        ]);
    }
} 