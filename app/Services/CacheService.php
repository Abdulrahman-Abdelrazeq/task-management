<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    public function clearItemCache(string $item): void
    {
        $cacheKeysList = Cache::get($item . '_cache_keys', []);

        foreach ($cacheKeysList as $key) {
            Cache::forget($key);
        }

        Cache::forget($item . '_cache_keys');
    }

    public function addKeyToCacheList(string $item, string $cacheKey): void
    {
        $cacheKeys = Cache::get($item . '_cache_keys', []);
        if (!in_array($cacheKey, $cacheKeys)) {
            $cacheKeys[] = $cacheKey;
            Cache::put($item . '_cache_keys', $cacheKeys, now()->addDays(1));
        }
    }
}
