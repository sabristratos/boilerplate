<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\Translatable\HasTranslations;

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
    use HasFactory;
    use HasTranslations;

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'taxonomy_id',
        'name',
        'slug',
        'description',
        'parent_id',
        'order',
        'meta',
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

    /**
     * Get all descendants of this term (children, children of children, etc.).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Term>
     */
    public function descendants(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->children()->with('descendants')->get();
    }
    /**
     * Get the available locales as a string.
     */
    protected function availableLocalesAsString(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: fn() => collect($this->getTranslatedLocales('name'))
            ->filter()
            ->implode(', '));
    }
    /**
     * Get the first available locale.
     */
    protected function firstAvailableLocale(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(get: function () {
            $locales = $this->getTranslatedLocales('name');
            return array_shift($locales);
        });
    }
    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'order' => 'integer',
        ];
    }
}
