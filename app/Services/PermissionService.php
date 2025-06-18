<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PermissionService
{
    protected const CACHE_KEY = 'permissions.all';
    protected const CACHE_TTL = 3600; // 1 hour

    public function all(): Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn() => Permission::all());
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
} 