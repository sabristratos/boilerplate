<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\ActivityLogger;
use App\Models\Taxonomy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TaxonomyService
{
    protected const CACHE_TTL = 3600; // 1 hour

    public function all(): Collection
    {
        return Cache::remember('taxonomies.all', self::CACHE_TTL, fn() => Taxonomy::with('terms')->get());
    }

    public function find(int $id): ?Taxonomy
    {
        return Cache::remember("taxonomies.{$id}", self::CACHE_TTL, fn() => Taxonomy::with('terms')->find($id));
    }

    public function create(array $data): Taxonomy
    {
        $taxonomy = new Taxonomy();
        $taxonomy->slug = $this->generateUniqueSlug($data['name'][config('app.fallback_locale')]);
        $taxonomy->hierarchical = $data['hierarchical'];

        foreach ($data['name'] as $locale => $name) {
            if (!empty($name)) {
                $taxonomy->setTranslation('name', $locale, $name);
            }
        }

        foreach ($data['description'] as $locale => $description) {
            if (!empty($description)) {
                $taxonomy->setTranslation('description', $locale, $description);
            }
        }

        $taxonomy->save();
        
        ActivityLogger::logCreated(
            $taxonomy,
            auth()->user(),
            $taxonomy->toArray(),
            'taxonomy'
        );

        $this->clearCache();

        return $taxonomy;
    }

    public function update(Taxonomy $taxonomy, array $data): Taxonomy
    {
        $oldValues = $taxonomy->getOriginal();
        $newSlug = Str::slug($data['name'][config('app.fallback_locale')]);
        if ($taxonomy->slug !== $newSlug) {
            $taxonomy->slug = $this->generateUniqueSlug($newSlug);
        }
        
        $taxonomy->hierarchical = $data['hierarchical'];

        foreach ($data['name'] as $locale => $name) {
            if (!empty($name)) {
                $taxonomy->setTranslation('name', $locale, $name);
            }
        }

        foreach ($data['description'] as $locale => $description) {
            if (!empty($description)) {
                $taxonomy->setTranslation('description', $locale, $description);
            }
        }

        $taxonomy->save();

        ActivityLogger::logUpdated(
            $taxonomy,
            auth()->user(),
            [
                'old' => $oldValues,
                'new' => $taxonomy->toArray(),
            ],
            'taxonomy'
        );

        $this->clearCache($taxonomy->id);

        return $taxonomy;
    }

    public function delete(Taxonomy $taxonomy): void
    {
        ActivityLogger::logDeleted(
            $taxonomy,
            auth()->user(),
            $taxonomy->toArray(),
            'taxonomy'
        );

        $this->clearCache($taxonomy->id);
        $taxonomy->delete();
    }

    public function clearCache(?int $id = null): void
    {
        Cache::forget('taxonomies.all');
        if ($id) {
            Cache::forget("taxonomies.{$id}");
        }
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (Taxonomy::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }
} 