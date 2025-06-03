<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * @property-read Taxonomy $taxonomy
 * @property int $id
 * @property int $taxonomy_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property int|null $parent_id
 * @property int $order
 * @property array|null $meta
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Term|null $parent
 * @property-read Collection|Term[] $children
 * @property-read Collection|Model[] $taxonomables
 */
class Term extends Model
{
    protected $fillable = [
        'taxonomy_id',
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
        'order' => 'integer',
    ];

    /**
     * Get the taxonomy that the term belongs to.
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class);
    }

    /**
     * Get the parent term.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'parent_id');
    }

    /**
     * Get the child terms.
     */
    public function children()
    {
        return $this->hasMany(Term::class, 'parent_id');
    }

    /**
     * Get all models that are assigned this term.
     */
    public function taxonomables(): MorphToMany
    {
        return $this->morphedByMany(Model::class, 'taxonomable');
    }

    /**
     * Scope a query to find terms by taxonomy slug.
     */
    public function scopeTaxonomySlug($query, $slug)
    {
        return $query->whereHas('taxonomy', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    /**
     * Scope a query to order terms by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get the full hierarchical path of the term.
     */
    public function getPath(): array
    {
        $path = [$this];
        $term = $this;

        while ($term->parent) {
            $term = $term->parent;
            array_unshift($path, $term);
        }

        return $path;
    }
}
