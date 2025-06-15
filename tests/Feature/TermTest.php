<?php

namespace Tests\Feature;

use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Facades\Settings;

class TermTest extends TestCase
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
            Permission::factory()->create(['slug' => 'view-terms']),
            Permission::factory()->create(['slug' => 'create-terms']),
            Permission::factory()->create(['slug' => 'edit-terms']),
            Permission::factory()->create(['slug' => 'delete-terms']),
        ];
        $role->permissions()->sync(collect($permissions)->pluck('id'));
        $this->user->roles()->sync([$role->id]);
        $this->taxonomy = Taxonomy::factory()->create(['hierarchical' => true]);
    }

    public function test_can_view_terms_list()
    {
        $terms = Term::factory()->count(3)->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.index', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.index');
        foreach ($terms as $term) {
            $response->assertSee($term->getTranslation('name', 'en'));
        }
    }

    public function test_cannot_view_terms_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.index', $this->taxonomy));

        $response->assertForbidden();
    }

    public function test_can_create_term()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_cannot_create_term_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertForbidden();
    }

    public function test_can_update_term()
    {
        $term = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.edit', [$this->taxonomy, $term]));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_cannot_update_term_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));
        $term = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.edit', [$this->taxonomy, $term]));

        $response->assertForbidden();
    }

    public function test_can_delete_term()
    {
        $term = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->delete(route('admin.taxonomies.terms.destroy', [$this->taxonomy, $term]));

        $response->assertRedirect(route('admin.taxonomies.terms.index', $this->taxonomy));
        $this->assertDatabaseMissing('terms', [
            'id' => $term->id
        ]);
    }

    public function test_cannot_delete_term_without_permission()
    {
        $this->user->roles()->detach();
        $this->user->roles()->attach(Role::factory()->create(['slug' => 'user']));
        $term = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->delete(route('admin.taxonomies.terms.destroy', [$this->taxonomy, $term]));

        $response->assertForbidden();
        $this->assertDatabaseHas('terms', [
            'id' => $term->id
        ]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_validates_unique_slug_within_taxonomy()
    {
        $existingTerm = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id,
            'slug' => 'test-term'
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_allows_same_slug_in_different_taxonomies()
    {
        $otherTaxonomy = Taxonomy::factory()->create();
        $existingTerm = Term::factory()->create([
            'taxonomy_id' => $otherTaxonomy->id,
            'slug' => 'test-term'
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_can_create_hierarchical_term()
    {
        $parentTerm = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.create', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.manage-term');
    }

    public function test_supports_multiple_languages()
    {
        $term = Term::factory()->create([
            'taxonomy_id' => $this->taxonomy->id,
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
            ->get(route('admin.taxonomies.terms.edit', [$this->taxonomy, $term]));

        $response->assertStatus(200);
        $response->assertSee('English Name');
        $response->assertSee('French Name');
        $response->assertSee('Spanish Name');
    }

    public function test_can_reorder_terms()
    {
        $terms = Term::factory()->count(3)->create([
            'taxonomy_id' => $this->taxonomy->id
        ]);

        $response = $this->actingAs($this->user)
            ->withSession(['auth.two_factor.confirmed' => true])
            ->get(route('admin.taxonomies.terms.index', $this->taxonomy));

        $response->assertStatus(200);
        $response->assertSeeLivewire('admin.terms.index');
    }
} 