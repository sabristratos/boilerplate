<?php

namespace Tests\Feature;

use App\Crud\Configurations\UserCrudConfig;
use App\Livewire\Admin\Crud\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use App\Models\Permission;
use App\Models\Role;
use Tests\TestCase;

class CrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $regularUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        $adminRole = Role::create(['name' => ['en' => 'Admin'], 'slug' => 'admin']);
        $userRole = Role::create(['name' => ['en' => 'User'], 'slug' => 'user']);

        $viewUsersPermission = Permission::create(['name' => ['en' => 'View Users'], 'slug' => 'users.viewAny']);
        Permission::create(['name' => ['en' => 'Create Users'], 'slug' => 'users.create']);
        Permission::create(['name' => ['en' => 'Update Users'], 'slug' => 'users.update']);
        Permission::create(['name' => ['en' => 'Delete Users'], 'slug' => 'users.delete']);

        $adminRole->permissions()->attach($viewUsersPermission);

        // Create users
        $this->adminUser = User::factory()->create();
        $this->adminUser->roles()->attach($adminRole);

        $this->regularUser = User::factory()->create();
        $this->regularUser->roles()->attach($userRole);

        User::factory(10)->create();
    }

    public function test_admin_user_can_view_crud_index_page()
    {
        $this->actingAs($this->adminUser);

        Livewire::test(Index::class, ['alias' => 'users', 'crud_config_class' => UserCrudConfig::class])
            ->assertStatus(200)
            ->assertViewIs('livewire.admin.crud.index');
    }

    public function test_unauthorized_user_is_redirected_from_crud_index_page()
    {
        $this->actingAs($this->regularUser);

        Livewire::test(Index::class, ['alias' => 'users', 'crud_config_class' => UserCrudConfig::class])
            ->assertForbidden();
    }

    public function test_it_can_search_for_users()
    {
        $this->actingAs($this->adminUser);
        $searchUser = User::factory()->create(['name' => 'Searchable User']);
        $userRole = Role::where('slug', 'user')->first();
        $searchUser->roles()->attach($userRole);

        Livewire::test(Index::class, ['alias' => 'users', 'crud_config_class' => UserCrudConfig::class])
            ->set('search', 'Searchable User')
            ->assertSee($searchUser->name);
    }

    public function test_it_can_filter_users_by_status()
    {
        $this->actingAs($this->adminUser);
        $activeUser = User::factory()->create(['status' => \App\Enums\UserStatus::Active]);
        $inactiveUser = User::factory()->create(['status' => \App\Enums\UserStatus::Inactive]);

        Livewire::test(Index::class, ['alias' => 'users', 'crud_config_class' => UserCrudConfig::class])
            ->set('filters.status', \App\Enums\UserStatus::Active->value)
            ->assertSee($activeUser->name)
            ->assertDontSee($inactiveUser->name);
    }

    public function test_admin_user_can_delete_a_user()
    {
        $this->actingAs($this->adminUser);
        $userToDelete = User::factory()->create();

        Livewire::test(Index::class, ['alias' => 'users', 'crud_config_class' => UserCrudConfig::class])
            ->call('askToDelete', $userToDelete->id)
            ->call('delete');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }
} 