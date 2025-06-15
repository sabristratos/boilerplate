<?php

namespace Tests\Feature;

use App\Models\Taxonomy;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Facades\Settings;
use App\Models\Term;

class TaxonomyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Settings::shouldReceive('get')->andReturnUsing(function ($key, $default = null) {
            return match ($key) {
                'require_two_factor_auth' => false,
                'default_language' => 'en',
                default => $default,
            };
        });
        $this->user = User::factory()->create([
            'two_factor_secret' => 'secret',
            'two_factor_confirmed_at' => now(),
            'email_verified_at' => now(),
        ]);
        $role = Role::factory()->create(['slug' => 'admin']);
        $permissions = [
            Permission::factory()->create(['slug' => 'view-taxonomies']),
            Permission::factory()->create(['slug' => 'create-taxonomies']),
            Permission::factory()->create(['slug' => 'edit-taxonomies']),
            Permission::factory()->create(['slug' => 'delete-taxonomies']),
        ];
        $role->permissions()->sync(collect($permissions)->pluck('id'));
        $this->user->roles()->sync([$role->id]);
    }

    public function test_can_view_taxonomies_list()
    {
        $taxonomies = Taxonomy::factory()->count(3)->create();

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.index'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.index');
        foreach ($taxonomies as $taxonomy) {
            $response->assertSee($taxonomy->getTranslation('name', 'en'));
        }
    }

    public function test_cannot_view_taxonomies_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.index'));

        $response->assertForbidden();
    }

    public function test_can_create_taxonomy()
    {
        $taxonomyData = [
            'name' => [
                'en' => 'Test Taxonomy',
                'fr' => 'Taxonomie Test'
            ],
            'description' => [
                'en' => 'Test Description',
                'fr' => 'Description Test'
            ],
            'hierarchical' => true
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.manage-taxonomy');
    }

    public function test_cannot_create_taxonomy_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.create'));

        $response->assertForbidden();
    }

    public function test_can_update_taxonomy()
    {
        $taxonomy = Taxonomy::factory()->create();

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.edit', $taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.manage-taxonomy');
    }

    public function test_cannot_update_taxonomy_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));
        $taxonomy = Taxonomy::factory()->create();

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.edit', $taxonomy));

        $response->assertForbidden();
    }

    public function test_can_delete_taxonomy()
    {
        $taxonomy = Taxonomy::factory()->create();

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->delete(route('admin.taxonomies.destroy', $taxonomy));

        $response->assertRedirect(route('admin.taxonomies.index'));
        $this->assertDatabaseMissing('taxonomies', [
            'id' => $taxonomy->id
        ]);
    }

    public function test_cannot_delete_taxonomy_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));
        $taxonomy = Taxonomy::factory()->create();

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->delete(route('admin.taxonomies.destroy', $taxonomy));

        $response->assertForbidden();
        $this->assertDatabaseHas('taxonomies', [
            'id' => $taxonomy->id
        ]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.manage-taxonomy');
    }

    public function test_validates_unique_slug()
    {
        $existingTaxonomy = Taxonomy::factory()->create(['slug' => 'test-taxonomy']);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.create'));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.manage-taxonomy');
    }

    public function test_can_toggle_hierarchical()
    {
        $taxonomy = Taxonomy::factory()->create(['hierarchical' => false]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.edit', $taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.taxonomies.manage-taxonomy');
    }

    public function test_supports_multiple_languages()
    {
        $taxonomy = Taxonomy::factory()->create([
            'name' => [
                'en' => 'English Name',
                'fr' => 'French Name',
                'es' => 'Spanish Name'
            ],
            'description' => [
                'en' => 'English Description',
                'fr' => 'French Description',
                'es' => 'Spanish Description'
            ]
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.edit', $taxonomy));

        $response->assertStatus(200);
        $response->assertSee('English Name');
        $response->assertSee('French Name');
        $response->assertSee('Spanish Name');
    }

    public function test_can_view_taxonomy_terms()
    {
        $taxonomy = Taxonomy::factory()->create();
        $terms = Term::factory()->count(3)->create([
            'taxonomy_id' => $taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.index', $taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.index');
        foreach ($terms as $term) {
            $response->assertSee($term->getTranslation('name', 'en'));
        }
    }
} 