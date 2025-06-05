<?php

namespace App\Livewire\Admin;

use App\Facades\ActivityLogger;
use App\Models\User;
use App\Models\Role;
use App\Notifications\Admin\UserCreatedNotification;
use App\Notifications\Admin\UserDeletedNotification;
use App\Notifications\User\NewUserWelcomeNotification;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Manages users within the admin panel.
 *
 * This component handles the creation, reading, updating, and deletion (CRUD)
 * of user accounts. It includes functionality for assigning roles, searching,
 * pagination, and uses modals for form interactions and confirmations.
 */
#[Layout('components.admin-layout')]
class UserManagement extends Component
{
    use WithPagination;

    public ?int $user_id = null;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $selectedRoles = [];

    public bool $isCreating = false;
    public bool $isEditing = false;
    public bool $confirmingDelete = false;
    public bool $showModal = false;

    public string $search = '';
    public int $perPage = 10;

    /**
     * Defines the validation rules for the component's properties.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $emailRules = ['required', 'email', 'max:255'];
        if ($this->isCreating || !$this->user_id) {
            $emailRules[] = 'unique:users,email';
        } else {
            $emailRules[] = Rule::unique('users', 'email')->ignore($this->user_id);
        }

        return [
            'name' => 'required|string|max:255',
            'email' => $emailRules,
            'password' => $this->isCreating || !empty($this->password) ? 'required|min:8|confirmed' : 'nullable|min:8|confirmed',
            'password_confirmation' => $this->isCreating || !empty($this->password) ? 'required' : 'nullable',
            'selectedRoles' => 'array',
        ];
    }

    /**
     * Defines custom validation messages.
     *
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'name.required' => __('The name field is required.'),
            'email.required' => __('The email field is required.'),
            'email.email' => __('The email must be a valid email address.'),
            'email.unique' => __('This email address is already in use.'),
            'password.required' => __('The password field is required.'),
            'password.min' => __('The password must be at least 8 characters.'),
            'password.confirmed' => __('The password confirmation does not match.'),
            'password_confirmation.required' => __('The password confirmation field is required when setting a new password.'),
        ];
    }

    /**
     * Resets pagination when the search query is updated.
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Resets pagination when the number of items per page is updated.
     * @return void
     */
    public function updatedPerPage(): void
    {
        $this->resetPage();
    }


    /**
     * Prepares the component for creating a new user.
     * Resets form fields and opens the modal.
     *
     * @return void
     */
    public function create(): void
    {
        $this->resetValidation();
        $this->reset(['user_id', 'name', 'email', 'password', 'password_confirmation', 'selectedRoles']);
        $this->isCreating = true;
        $this->isEditing = false;
        $this->confirmingDelete = false;
        $this->showModal = true;
    }

    /**
     * Stores a newly created user in the database.
     * Validates input, creates the user, assigns roles, logs the activity,
     * and displays a success toast.
     *
     * @return void
     */
    public function store(): void
    {
        $this->validate();

        try {
            $plainPassword = $this->password; 
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
            ]);

            if (!empty($this->selectedRoles)) {
                $user->roles()->attach($this->selectedRoles);
            }

            // Log activity
            ActivityLogger::logCreated(
                $user,
                auth()->user(),
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $this->selectedRoles,
                ],
                'user'
            );

            // Send welcome email to the new user
            try {
                $user->notify(new NewUserWelcomeNotification($user, $plainPassword));
            } catch (\Exception $e) {
                Log::error('Failed to send welcome email to user ' . $user->email . ': ' . $e->getMessage());
                // Non-critical, so we don't re-throw or show a user-facing error for this
            }

            // Notify admin users
            try {
                $adminUsers = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->get();
                if ($adminUsers->isNotEmpty() && auth()->check()) {
                    Notification::send($adminUsers, new UserCreatedNotification($user, auth()->user()));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send UserCreatedNotification to admins: ' . $e->getMessage());
            }

            $this->closeModal();
            Flux::toast(
                text: __('User created successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to store user: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to create user. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Prepares the component for editing an existing user.
     * Loads user data into form fields and opens the modal.
     *
     * @param User $user The user instance to edit.
     * @return void
     */
    public function edit(User $user): void
    {
        $this->resetValidation();
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = $user->roles->pluck('id')->map(fn ($id) => (string)$id)->toArray();

        $this->isCreating = false;
        $this->isEditing = true;
        $this->confirmingDelete = false;
        $this->showModal = true;
    }

    /**
     * Updates an existing user in the database.
     * Validates input, updates user details, syncs roles, logs activity,
     * and displays a success toast.
     *
     * @return void
     */
    public function update(): void
    {
        if (!$this->user_id) {
            Flux::toast(text: __('No user selected for update.'), heading: __('Error'), variant: 'danger');
            return;
        }
        $this->validate();

        try {
            $user = User::findOrFail($this->user_id);

            $oldValues = [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('id')->map(fn ($id) => (string)$id)->toArray(),
            ];

            $userData = [
                'name' => $this->name,
                'email' => $this->email,
            ];

            if (!empty($this->password)) {
                $userData['password'] = Hash::make($this->password);
            }

            $user->update($userData);
            $user->roles()->sync($this->selectedRoles);

            ActivityLogger::logUpdated(
                $user,
                auth()->user(),
                [
                    'old' => $oldValues,
                    'new' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $this->selectedRoles,
                        'password_changed' => !empty($this->password),
                    ],
                ],
                'user'
            );

            $this->closeModal();
            Flux::toast(
                text: __('User updated successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to update user: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to update user. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Prepares the component for confirming user deletion.
     * Sets the user to be deleted and opens the confirmation modal.
     *
     * @param User $user The user instance to delete.
     * @return void
     */
    public function confirmDelete(User $user): void
    {
        $this->user_id = $user->id;
        $this->name = $user->name;
        $this->isCreating = false;
        $this->isEditing = false;
        $this->confirmingDelete = true;
        $this->showModal = true;
    }

    /**
     * Deletes a user from the database.
     * Prevents self-deletion, logs activity, detaches roles,
     * and displays a success toast.
     *
     * @return void
     */
    public function delete(): void
    {
        if (!$this->user_id) {
            Flux::toast(text: __('No user selected for deletion.'), heading: __('Error'), variant: 'danger');
            return;
        }

        try {
            $user = User::findOrFail($this->user_id);
            $performingUser = auth()->user();

            if (!$performingUser) {
                 Log::warning('Attempted to delete user without an authenticated performing user.');
                 Flux::toast(text: __('Could not identify performing user. Action aborted.'), heading: __('Error'), variant: 'danger');
                 return;
            }


            if ($user->id === $performingUser->id) {
                Flux::toast(
                    text: __('You cannot delete your own account.'),
                    heading: __('Error'),
                    variant: 'danger'
                );
                $this->closeModal();
                return;
            }

            $userDataForNotification = [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];

            // Log activity before deletion
            ActivityLogger::logDeleted(
                $user,
                $performingUser,
                $userDataForNotification + [
                    'roles' => $user->roles->pluck('id')->toArray(),
                    'created_at' => $user->created_at->toDateTimeString(),
                ],
                'user'
            );

            $user->roles()->detach();
            $user->delete();

            // Notify admin users
            try {
                $adminUsers = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))->get();
                if ($adminUsers->isNotEmpty()) {
                    Notification::send($adminUsers, new UserDeletedNotification($userDataForNotification, $performingUser));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send UserDeletedNotification to admins: ' . $e->getMessage());
            }

            $this->closeModal();
            Flux::toast(
                text: __('User deleted successfully.'),
                heading: __('Success'),
                variant: 'success'
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete user: ' . $e->getMessage());
            Flux::toast(
                text: __('Failed to delete user. Please try again.'),
                heading: __('Error'),
                variant: 'danger'
            );
        }
    }

    /**
     * Closes any active modal and resets form state and validation.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->isCreating = false;
        $this->isEditing = false;
        $this->confirmingDelete = false;
        $this->reset(['user_id', 'name', 'email', 'password', 'password_confirmation', 'selectedRoles']);
        $this->resetValidation();
    }

    /**
     * Renders the component.
     * Fetches users and roles for display in the view, applying search and pagination.
     *
     * @return View
     */
    public function render(): View
    {
        $usersQuery = User::query();

        if (!empty($this->search)) {
            $usersQuery->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $usersQuery->with('roles')->latest()->paginate($this->perPage);
        $roles = Role::all();

        return view('livewire.admin.user-management', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }
}
