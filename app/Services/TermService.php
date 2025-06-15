<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Term;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TermService
{
    protected const CACHE_TTL = 3600; // 1 hour

    public function find(int $id): ?Term
    {
        return Cache::remember("terms.{$id}", self::CACHE_TTL, function () use ($id) {
            return Term::find($id);
        });
    }

    public function getByTaxonomy(int $taxonomyId): Collection
    {
        return Cache::remember("terms.taxonomy.{$taxonomyId}", self::CACHE_TTL, function () use ($taxonomyId) {
            return Term::where('taxonomy_id', $taxonomyId)->orderBy('order')->get();
        });
    }

    public function create(array $data): Term
    {
        $term = new Term();
        $term->slug = $this->generateUniqueSlug($data['name'][config('app.fallback_locale')], $data['taxonomy_id']);
        $term->taxonomy_id = $data['taxonomy_id'];
        $term->parent_id = $data['parent_id'] ?? null;
        $term->order = $data['order'] ?? 0;

        foreach ($data['name'] as $locale => $name) {
            if (!empty($name)) {
                $term->setTranslation('name', $locale, $name);
            }
        }

        foreach ($data['description'] as $locale => $description) {
            if (!empty($description)) {
                $term->setTranslation('description', $locale, $description);
            }
        }

        $term->save();

        ActivityLogger::logCreated(
            $term,
            auth()->user(),
            $term->toArray(),
            'term'
        );

        $this->clearCache(null, $term->taxonomy_id);

        return $term;
    }

    public function update(Term $term, array $data): Term
    {
        $oldValues = $term->getOriginal();
        $newSlug = Str::slug($data['name'][config('app.fallback_locale')]);
        if ($term->slug !== $newSlug) {
            $term->slug = $this->generateUniqueSlug($newSlug, $term->taxonomy_id);
        }

        $term->parent_id = $data['parent_id'] ?? null;
        $term->order = $data['order'] ?? $term->order;

        foreach ($data['name'] as $locale => $name) {
            if (!empty($name)) {
                $term->setTranslation('name', $locale, $name);
            }
        }

        foreach ($data['description'] as $locale => $description) {
            if (!empty($description)) {
                $term->setTranslation('description', $locale, $description);
            }
        }

        $term->save();

        ActivityLogger::logUpdated(
            $term,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => $term->toArray(),
            ],
            'term'
        );

        $this->clearCache($term->id, $term->taxonomy_id);

        return $term;
    }

    public function delete(Term $term): void
    {
        ActivityLogger::logDeleted(
            $term,
            auth()->user(),
            $term->toArray(),
            'term'
        );

        $this->clearCache($term->id, $term->taxonomy_id);
        $term->delete();
    }

    public function clearCache(?int $id = null, ?int $taxonomyId = null): void
    {
        if ($id) {
            Cache::forget("terms.{$id}");
        }
        if ($taxonomyId) {
            Cache::forget("terms.taxonomy.{$taxonomyId}");
        }
    }

    private function generateUniqueSlug(string $name, int $taxonomyId): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Term::where('taxonomy_id', $taxonomyId)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
} 