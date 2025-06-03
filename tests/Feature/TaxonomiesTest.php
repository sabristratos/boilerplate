<?php

namespace Tests\Feature;

use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\User;
use App\Models\Traits\HasTaxonomies;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaxonomiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_taxonomy()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
            'description' => 'Post categories',
            'hierarchical' => true,
        ]);

        // Check if taxonomy was created
        $this->assertDatabaseHas('taxonomies', [
            'name' => 'Categories',
            'slug' => 'categories',
            'description' => 'Post categories',
            'hierarchical' => true,
        ]);

        // Check if the taxonomy has the correct attributes
        $this->assertEquals('Categories', $taxonomy->name);
        $this->assertEquals('categories', $taxonomy->slug);
        $this->assertEquals('Post categories', $taxonomy->description);
        $this->assertTrue($taxonomy->hierarchical);
    }

    public function test_can_create_term()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
            'description' => 'Post categories',
            'hierarchical' => true,
        ]);

        // Create a term
        $term = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology posts',
            'order' => 1,
        ]);

        // Check if term was created
        $this->assertDatabaseHas('terms', [
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology posts',
            'order' => 1,
        ]);

        // Check if the term has the correct attributes
        $this->assertEquals('Technology', $term->name);
        $this->assertEquals('technology', $term->slug);
        $this->assertEquals('Technology posts', $term->description);
        $this->assertEquals(1, $term->order);
        $this->assertEquals($taxonomy->id, $term->taxonomy_id);
    }

    public function test_can_create_hierarchical_terms()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
            'description' => 'Post categories',
            'hierarchical' => true,
        ]);

        // Create a parent term
        $parentTerm = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Technology',
            'slug' => 'technology',
            'description' => 'Technology posts',
            'order' => 1,
        ]);

        // Create a child term
        $childTerm = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Programming',
            'slug' => 'programming',
            'description' => 'Programming posts',
            'parent_id' => $parentTerm->id,
            'order' => 1,
        ]);

        // Check if child term was created with correct parent
        $this->assertDatabaseHas('terms', [
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Programming',
            'slug' => 'programming',
            'parent_id' => $parentTerm->id,
        ]);

        // Check parent-child relationship
        $this->assertEquals($parentTerm->id, $childTerm->parent_id);
        $this->assertTrue($parentTerm->children->contains($childTerm));
        $this->assertEquals($parentTerm->id, $childTerm->parent->id);
    }

    public function test_term_path_returns_correct_hierarchy()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
            'hierarchical' => true,
        ]);

        // Create a hierarchy of terms
        $level1 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Level 1',
            'slug' => 'level-1',
        ]);

        $level2 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Level 2',
            'slug' => 'level-2',
            'parent_id' => $level1->id,
        ]);

        $level3 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Level 3',
            'slug' => 'level-3',
            'parent_id' => $level2->id,
        ]);

        // Get the path for level 3
        $path = $level3->getPath();

        // Check if path contains all levels in correct order
        $this->assertCount(3, $path);
        $this->assertEquals($level1->id, $path[0]->id);
        $this->assertEquals($level2->id, $path[1]->id);
        $this->assertEquals($level3->id, $path[2]->id);
    }

    public function test_taxonomy_scope_methods()
    {
        // Create taxonomies
        $taxonomy1 = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
        ]);

        $taxonomy2 = Taxonomy::create([
            'name' => 'Tags',
            'slug' => 'tags',
        ]);

        // Test the slug scope
        $foundTaxonomy = Taxonomy::slug('categories')->first();
        $this->assertEquals($taxonomy1->id, $foundTaxonomy->id);

        $foundTaxonomy = Taxonomy::slug('tags')->first();
        $this->assertEquals($taxonomy2->id, $foundTaxonomy->id);
    }

    public function test_term_scope_methods()
    {
        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'Categories',
            'slug' => 'categories',
        ]);

        // Create terms with different orders
        $term1 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Term 1',
            'slug' => 'term-1',
            'order' => 2,
        ]);

        $term2 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Term 2',
            'slug' => 'term-2',
            'order' => 1,
        ]);

        // Test the taxonomySlug scope
        $terms = Term::taxonomySlug('categories')->get();
        $this->assertCount(2, $terms);
        $this->assertTrue($terms->contains($term1));
        $this->assertTrue($terms->contains($term2));

        // Test the ordered scope
        $orderedTerms = Term::ordered()->get();
        $this->assertEquals($term2->id, $orderedTerms[0]->id);
        $this->assertEquals($term1->id, $orderedTerms[1]->id);
    }

    public function test_model_can_have_taxonomies()
    {
        // Create a user that uses the HasTaxonomies trait
        $user = User::factory()->create();

        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'User Categories',
            'slug' => 'user-categories',
        ]);

        // Create terms
        $term1 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $term2 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Editor',
            'slug' => 'editor',
        ]);

        // Add terms to user
        $user->addTerm($term1);
        $user->addTerm($term2);

        // Check if user has terms
        $this->assertTrue($user->hasTerm($term1));
        $this->assertTrue($user->hasTerm($term2));

        // Get terms for user
        $userTerms = $user->terms;
        $this->assertCount(2, $userTerms);
        $this->assertTrue($userTerms->contains($term1));
        $this->assertTrue($userTerms->contains($term2));

        // Get terms by taxonomy
        $categoryTerms = $user->getTerms('user-categories');
        $this->assertCount(2, $categoryTerms);

        // Check if user has terms from taxonomy
        $this->assertTrue($user->hasTermsFromTaxonomy('user-categories'));
    }

    public function test_model_can_sync_terms()
    {
        // Create a user that uses the HasTaxonomies trait
        $user = User::factory()->create();

        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'User Categories',
            'slug' => 'user-categories',
        ]);

        // Create terms
        $term1 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $term2 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Editor',
            'slug' => 'editor',
        ]);

        $term3 = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Subscriber',
            'slug' => 'subscriber',
        ]);

        // Add initial terms
        $user->addTerm($term1);
        $user->addTerm($term2);

        // Check initial terms
        $this->assertTrue($user->hasTerm($term1));
        $this->assertTrue($user->hasTerm($term2));
        $this->assertFalse($user->hasTerm($term3));

        // Sync terms (remove term1, keep term2, add term3)
        $user->syncTerms([$term2->id, $term3->id]);

        // Refresh user model
        $user->refresh();

        // Check synced terms
        $this->assertFalse($user->hasTerm($term1));
        $this->assertTrue($user->hasTerm($term2));
        $this->assertTrue($user->hasTerm($term3));

        // Get terms for user
        $userTerms = $user->terms;
        $this->assertCount(2, $userTerms);
        $this->assertTrue($userTerms->contains($term2));
        $this->assertTrue($userTerms->contains($term3));
    }

    public function test_model_can_remove_term()
    {
        // Create a user that uses the HasTaxonomies trait
        $user = User::factory()->create();

        // Create a taxonomy
        $taxonomy = Taxonomy::create([
            'name' => 'User Categories',
            'slug' => 'user-categories',
        ]);

        // Create a term
        $term = Term::create([
            'taxonomy_id' => $taxonomy->id,
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        // Add term to user
        $user->addTerm($term);

        // Check if user has term
        $this->assertTrue($user->hasTerm($term));

        // Remove term
        $user->removeTerm($term);

        // Check if term was removed
        $this->assertFalse($user->hasTerm($term));
    }
}
