<?php

namespace Tests\Feature;

use App\Livewire\Admin\TaxonomyManagement;
use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class TaxonomyManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create and authenticate an admin user
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_taxonomy_management_component_can_render()
    {
        // Test that the component renders
        Livewire::test(TaxonomyManagement::class)
            ->assertStatus(200);
    }

    public function test_can_create_taxonomy()
    {
        // Test creating a taxonomy
        Livewire::test(TaxonomyManagement::class)
            ->set('taxonomyName', 'Test Taxonomy')
            ->set('taxonomyDescription', 'This is a test taxonomy')
            ->set('taxonomyHierarchical', true)
            ->call('createTaxonomy')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('toast');

        // Check if taxonomy was created in the database
        $this->assertDatabaseHas('taxonomies', [
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);
    }

    public function test_cannot_create_taxonomy_with_duplicate_slug()
    {
        // Create a taxonomy first
        Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Try to create another taxonomy with the same name (which will generate the same slug)
        Livewire::test(TaxonomyManagement::class)
            ->set('taxonomyName', 'Test Taxonomy')
            ->set('taxonomyDescription', 'This is another test taxonomy')
            ->set('taxonomyHierarchical', false)
            ->call('createTaxonomy')
            ->assertHasErrors(['taxonomyName']);
    }

    public function test_can_edit_taxonomy()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Original Name',
            'slug' => 'original-name',
            'description' => 'Original description',
            'hierarchical' => false,
        ]);

        // Test editing the taxonomy
        Livewire::test(TaxonomyManagement::class)
            ->call('editTaxonomy', $taxonomy)
            ->assertSet('editingTaxonomyId', $taxonomy->id)
            ->assertSet('taxonomyName', 'Original Name')
            ->assertSet('taxonomyDescription', 'Original description')
            ->assertSet('taxonomyHierarchical', false)
            ->set('taxonomyName', 'Updated Name')
            ->set('taxonomyDescription', 'Updated description')
            ->set('taxonomyHierarchical', true)
            ->call('updateTaxonomy')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('toast');

        // Check if taxonomy was updated in the database
        $this->assertDatabaseHas('taxonomies', [
            'id' => $taxonomy->id,
            'name' => 'Updated Name',
            'slug' => 'updated-name',
            'description' => 'Updated description',
            'hierarchical' => true,
        ]);
    }

    public function test_can_delete_taxonomy()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Test deleting the taxonomy
        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTaxonomy', $taxonomy)
            ->assertDispatchedBrowserEvent('toast');

        // Check if taxonomy was deleted from the database
        $this->assertDatabaseMissing('taxonomies', [
            'id' => $taxonomy->id,
        ]);
    }

    public function test_can_create_term()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Test creating a term
        Livewire::test(TaxonomyManagement::class)
            ->set('selectedTaxonomyId', $taxonomy->id)
            ->set('termName', 'Test Term')
            ->set('termDescription', 'This is a test term')
            ->set('termOrder', 1)
            ->call('createTerm')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('toast');

        // Check if term was created in the database
        $this->assertDatabaseHas('terms', [
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Test Term',
            'slug' => 'test-term',
            'description' => 'This is a test term',
            'order' => 1,
        ]);
    }

    public function test_cannot_create_term_with_duplicate_slug_in_same_taxonomy()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Create a term
        Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Test Term',
            'slug' => 'test-term',
            'description' => 'This is a test term',
            'order' => 1,
        ]);

        // Try to create another term with the same name (which will generate the same slug)
        Livewire::test(TaxonomyManagement::class)
            ->set('selectedTaxonomyId', $taxonomy->id)
            ->set('termName', 'Test Term')
            ->set('termDescription', 'This is another test term')
            ->set('termOrder', 2)
            ->call('createTerm')
            ->assertHasErrors(['termName']);
    }

    public function test_can_edit_term()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Create a term
        $term = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Original Term',
            'slug' => 'original-term',
            'description' => 'Original description',
            'order' => 1,
        ]);

        // Test editing the term
        Livewire::test(TaxonomyManagement::class)
            ->call('editTerm', $term)
            ->assertSet('editingTermId', $term->id)
            ->assertSet('selectedTaxonomyId', $taxonomy->id)
            ->assertSet('termName', 'Original Term')
            ->assertSet('termDescription', 'Original description')
            ->assertSet('termOrder', 1)
            ->set('termName', 'Updated Term')
            ->set('termDescription', 'Updated description')
            ->set('termOrder', 2)
            ->call('updateTerm')
            ->assertHasNoErrors()
            ->assertDispatchedBrowserEvent('toast');

        // Check if term was updated in the database
        $this->assertDatabaseHas('terms', [
            'id' => $term->id,
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Updated Term',
            'slug' => 'updated-term',
            'description' => 'Updated description',
            'order' => 2,
        ]);
    }

    public function test_can_delete_term()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Create a term
        $term = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Test Term',
            'slug' => 'test-term',
            'description' => 'This is a test term',
            'order' => 1,
        ]);

        // Test deleting the term
        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTerm', $term)
            ->assertDispatchedBrowserEvent('toast');

        // Check if term was deleted from the database
        $this->assertDatabaseMissing('terms', [
            'id' => $term->id,
        ]);
    }

    public function test_cannot_delete_term_with_children()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Test Taxonomy',
            'slug' => 'test-taxonomy',
            'description' => 'This is a test taxonomy',
            'hierarchical' => true,
        ]);

        // Create a parent term
        $parentTerm = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Parent Term',
            'slug' => 'parent-term',
            'description' => 'This is a parent term',
            'order' => 1,
        ]);

        // Create a child term
        $childTerm = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Child Term',
            'slug' => 'child-term',
            'description' => 'This is a child term',
            'parent_id' => $parentTerm->id,
            'order' => 1,
        ]);

        // Try to delete the parent term
        Livewire::test(TaxonomyManagement::class)
            ->call('deleteTerm', $parentTerm)
            ->assertDispatchedBrowserEvent('toast');

        // Check if parent term still exists in the database
        $this->assertDatabaseHas('terms', [
            'id' => $parentTerm->id,
        ]);
    }
}
