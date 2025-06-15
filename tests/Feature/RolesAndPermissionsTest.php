<?php

use App\Livewire\Admin\Roles\ManageRole;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

/**
 * @covers \App\Models\User
 * @covers \App\Models\Role
 */
it('can determine if a user has a specific permission', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission = Permission::factory()->create(['slug' => 'edit-articles']);

    $role->permissions()->attach($permission);
    $user->roles()->attach($role);

    expect($user->hasPermission('edit-articles'))->toBeTrue();
    expect($user->hasPermission('delete-articles'))->toBeFalse();
});

/**
 * @covers \App\Models\User
 * @covers \App\Models\Role
 */
it('can determine if a user has any of a given set of permissions', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create(['slug' => 'edit-articles']);
    $permission2 = Permission::factory()->create(['slug' => 'publish-articles']);
    $permission3 = Permission::factory()->create(['slug' => 'delete-articles']);

    $role->permissions()->attach([$permission1->id, $permission2->id]);
    $user->roles()->attach($role);

    expect($user->hasAnyPermission(['edit-articles', 'delete-articles']))->toBeTrue();
    expect($user->hasAnyPermission(['delete-articles', 'unpublish-articles']))->toBeFalse();
});

/**
 * @covers \App\Models\User
 * @covers \App\Models\Role
 */
it('can determine if a user has all of a given set of permissions', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create(['slug' => 'edit-articles']);
    $permission2 = Permission::factory()->create(['slug' => 'publish-articles']);
    $permission3 = Permission::factory()->create(['slug' => 'delete-articles']);

    $role->permissions()->attach([$permission1->id, $permission2->id]);
    $user->roles()->attach($role);

    expect($user->hasAllPermissions(['edit-articles', 'publish-articles']))->toBeTrue();
    expect($user->hasAllPermissions(['edit-articles', 'delete-articles']))->toBeFalse();
});

/**
 * @covers \App\Models\Role
 */
it('can determine if a role has a specific permission', function () {
    $role = Role::factory()->create();
    $permission = Permission::factory()->create(['slug' => 'edit-articles']);

    $role->permissions()->attach($permission);

    expect($role->hasPermission('edit-articles'))->toBeTrue();
    expect($role->hasPermission('delete-articles'))->toBeFalse();
});

/**
 * @covers \App\Livewire\Admin\Roles\ManageRole
 * @covers \App\Services\RoleService
 */
it('can create a role with permissions', function () {
    $user = User::factory()->create();
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();

    // Give user permission to create roles
    $user->roles()->create(['name' => 'admin', 'slug' => 'admin'])
        ->permissions()->create(['name' => 'Create Roles', 'slug' => 'create-roles']);

    actingAs($user);

    \Livewire\Livewire::test(ManageRole::class)
        ->set('name', 'Test Role')
        ->set('description', 'A test role.')
        ->set('selectedPermissions', [(string) $permission1->id, (string) $permission2->id])
        ->call('save');

    assertDatabaseHas('roles', [
        'name' => 'Test Role',
        'description' => 'A test role.',
    ]);

    $role = Role::where('name', 'Test Role')->first();
    expect($role->permissions->pluck('id')->all())->toEqualCanonicalizing([$permission1->id, $permission2->id]);
});

/**
 * @covers \App\Livewire\Admin\Roles\ManageRole
 * @covers \App\Services\RoleService
 */
it('can update a role with permissions', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permission1 = Permission::factory()->create();
    $permission2 = Permission::factory()->create();
    $permission3 = Permission::factory()->create();

    $role->permissions()->attach($permission1);

    // Give user permission to edit roles
    $user->roles()->create(['name' => 'admin', 'slug' => 'admin'])
        ->permissions()->create(['name' => 'Edit Roles', 'slug' => 'edit-roles']);

    actingAs($user);

    \Livewire\Livewire::test(ManageRole::class, ['role' => $role])
        ->set('name', 'Updated Role Name')
        ->set('description', 'Updated description.')
        ->set('selectedPermissions', [(string) $permission2->id, (string) $permission3->id])
        ->call('save')
        ->assertDispatched('role-saved');

    assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'Updated Role Name',
        'description' => 'Updated description.',
    ]);

    $role->refresh();
    expect($role->permissions)->toHaveCount(2)
        ->and($role->permissions->pluck('id')->all())->toEqualCanonicalizing([$permission2->id, $permission3->id]);
}); 