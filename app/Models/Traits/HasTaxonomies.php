<?php

namespace App\Models\Traits;

use App\Models\Term;
use App\Models\Taxonomy;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasTaxonomies
{
    /**
     * Get all terms for the model.
     */
    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'taxonomable');
    }

    /**
     * Get terms for a specific taxonomy.
     */
    public function getTerms(string $taxonomySlug)
    {
        return $this->terms()
            ->whereHas('taxonomy', function ($query) use ($taxonomySlug) {
                $query->where('slug', $taxonomySlug);
            })
            ->get();
    }

    /**
     * Add a term to the model.
     */
    public function addTerm(Term $term): void
    {
        $this->terms()->attach($term);
    }

    /**
     * Remove a term from the model.
     */
    public function removeTerm(Term $term): int
    {
        return $this->terms()->detach($term);
    }

    /**
     * Sync terms for the model.
     */
    public function syncTerms(array $termIds): array
    {
        return $this->terms()->sync($termIds);
    }

    /**
     * Check if the model has a specific term.
     */
    public function hasTerm(Term $term): bool
    {
        return $this->terms()->where('terms.id', $term->id)->exists();
    }

    /**
     * Check if the model has any terms from a specific taxonomy.
     */
    public function hasTermsFromTaxonomy(string $taxonomySlug): bool
    {
        return $this->terms()
            ->whereHas('taxonomy', function ($query) use ($taxonomySlug) {
                $query->where('slug', $taxonomySlug);
            })
            ->exists();
    }
}
