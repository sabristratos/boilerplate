<?php

namespace Tests\Feature;

use App\Livewire\Admin\TaxonomyManagement;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaxonomyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function can_create_taxonomy()
    {
        Livewire::test(TaxonomyManagement::class)
            ->set('taxonomyName', 'Categories')
            ->set('taxonomyDescription', 'Blog post categories')
            ->set('taxonomyHierarchical', true)
            ->call('storeTaxonomy');

        $this->assertDatabaseHas('taxonomies', [
            'name' => 'Categories',
            'slug' => 'categories',
            'description' => 'Blog post categories',
            'hierarchical' => true,
        ]);
    }

    /** @test */
    public function cannot_create_taxonomy_with_duplicate_slug()
    {
        Taxonomy::factory()->create(['name' => 'Categories', 'slug' => 'categories']);

        Livewire::test(TaxonomyManagement::class)
            ->set('taxonomyName', 'Categories')
            ->call('storeTaxonomy')
            ->assertHasErrors(['taxonomyName']);
    }

    /** @test */
    public function can_edit_taxonomy()
    {
        $taxonomy = Taxonomy::factory()->create();

        Livewire::test(TaxonomyManagement::class)
            ->call('openEditTaxonomyModal', $taxonomy)
            ->set('taxonomyName', 'Updated Categories')
            ->set('taxonomyDescription', 'Updated description')
            ->call('updateTaxonomy');

        $this->assertDatabaseHas('taxonomies', [
            'id' => $taxonomy->id,
            'name' => 'Updated Categories',
            'slug' => 'updated-categories',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function can_delete_taxonomy()
    {
        $taxonomy = Taxonomy::factory()->create();

        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTaxonomy', $taxonomy->id);

        $this->assertDatabaseMissing('taxonomies', ['id' => $taxonomy->id]);
    }

    /** @test */
    public function can_create_term()
    {
        $taxonomy = Taxonomy::factory()->create();

        Livewire::test(TaxonomyManagement::class)
            ->set('selectedTaxonomyId', $taxonomy->id)
            ->set('termName', 'Laravel')
            ->set('termDescription', 'About Laravel')
            ->call('storeTerm');

        $this->assertDatabaseHas('terms', [
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);
    }

    /** @test */
    public function cannot_create_term_with_duplicate_slug_in_same_taxonomy()
    {
        $taxonomy = Taxonomy::factory()->create();
        Term::factory()->create(['name' => 'Laravel', 'slug' => 'laravel', 'taxonomy_id' => $taxonomy->id]);

        Livewire::test(TaxonomyManagement::class)
            ->set('selectedTaxonomyId', $taxonomy->id)
            ->set('termName', 'Laravel')
            ->call('storeTerm')
            ->assertHasErrors(['termName']);
    }

    /** @test */
    public function can_edit_term()
    {
        $taxonomy = Taxonomy::factory()->create();
        $term = Term::factory()->create(['taxonomy_id' => $taxonomy->id]);

        Livewire::test(TaxonomyManagement::class)
            ->call('openEditTermModal', $term)
            ->set('termName', 'Updated Term')
            ->call('updateTerm');

        $this->assertDatabaseHas('terms', [
            'id' => $term->id,
            'name' => 'Updated Term',
            'slug' => 'updated-term',
        ]);
    }

    /** @test */
    public function can_delete_term()
    {
        $term = Term::factory()->create();

        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTerm', $term->id);

        $this->assertDatabaseMissing('terms', ['id' => $term->id]);
    }

    /** @test */
    public function cannot_delete_term_with_children()
    {
        $parent = Term::factory()->create();
        Term::factory()->create(['parent_id' => $parent->id]);

        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTerm', $parent->id);

        $this->assertDatabaseHas('terms', ['id' => $parent->id]);
    }
}
