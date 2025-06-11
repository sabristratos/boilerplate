<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'hierarchical',
    ];

    protected $casts = [
        'hierarchical' => 'boolean',
    ];

    /**
     * Get the terms for the taxonomy.
     */
    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    /**
     * Get the root terms for the taxonomy (terms without a parent).
     */
    public function rootTerms()
    {
        return $this->terms()->whereNull('parent_id');
    }

    /**
     * Scope a query to find a taxonomy by its slug.
     */
    public function scopeSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }
}
