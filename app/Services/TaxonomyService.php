<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Taxonomy;
use Illuminate\Support\Str;

class TaxonomyService
{
    public function createTaxonomy(array $data): Taxonomy
    {
        $data['slug'] = Str::slug($data['name']);
        $taxonomy = Taxonomy::create($data);

        ActivityLogger::logCreated(
            $taxonomy,
            auth()->user(),
            $taxonomy->toArray(),
            'taxonomy'
        );

        return $taxonomy;
    }

    public function updateTaxonomy(Taxonomy $taxonomy, array $data): Taxonomy
    {
        $oldValues = $taxonomy->getOriginal();
        $data['slug'] = Str::slug($data['name']);
        $taxonomy->update($data);

        ActivityLogger::logUpdated(
            $taxonomy,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => $taxonomy->toArray(),
            ],
            'taxonomy'
        );

        return $taxonomy;
    }

    public function deleteTaxonomy(Taxonomy $taxonomy): void
    {
        ActivityLogger::logDeleted(
            $taxonomy,
            auth()->user(),
            $taxonomy->toArray(),
            'taxonomy'
        );

        $taxonomy->delete();
    }
} 