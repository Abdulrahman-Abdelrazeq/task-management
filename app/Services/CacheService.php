<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    // Clear all cached keys for a specific resource (e.g., tasks).
    public function clearItemCache(string $item): void
    {
        // Retrieve all cache keys associated with the item
        $cacheKeysList = Cache::get($item . '_cache_keys', []);

        // Forget each individual cache key
        foreach ($cacheKeysList as $key) {
            Cache::forget($key);
        }

        // Forget the key list itself
        Cache::forget($item . '_cache_keys');
    }

    // Add a cache key to the tracked list of a resource
    public function addKeyToCacheList(string $item, string $cacheKey): void
    {
        // Retrieve current list of cache keys
        $cacheKeys = Cache::get($item . '_cache_keys', []);

        // Add the new key if not already tracked
        if (!in_array($cacheKey, $cacheKeys)) {
            $cacheKeys[] = $cacheKey;

            // Store the updated key list in cache for 1 day
            Cache::put($item . '_cache_keys', $cacheKeys, now()->addDays(1));
        }
    }
}
