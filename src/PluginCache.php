<?php

/**
 * Cache for the plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */

namespace DRP\DeviceImporter;

use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

/**
 * Cache for the plugin.
 *
 * @package     device-importer
 * @author      Daryl Peterson <@gmail.com>
 * @copyright   Copyright (c) 2026, Daryl Peterson
 * @license     https://opensource.org MIT License
 * @link        https://github.com/daryl-peterson/
 * @since       0.0.1
 */
class PluginCache {
    const CACHE_TAG = 'device_importer';
    const TTL = 600; // 5 minutes

    public function __construct() {

        # Code Here
    }

    /**
     * Retrieve an item from the cache by key.
     *
     * @param UnitEnum|string $cacheKey The cache key to retrieve the value for.
     * @param mixed $default The default value to return if the cache key does not exist.
     * @return mixed
     *
     * @since 0.0.1
     */
    public static function get(UnitEnum|string $cacheKey, mixed $default = null): mixed {

        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->get($cacheKey, $default);
        }

        return Cache::get($cacheKey, $default);
    }

    /**
     * Store an item in the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to store the value for.
     * @param mixed $cacheValue The value to store in the cache.
     * @param int $ttl The time-to-live for the cache entry, in seconds.
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function set(UnitEnum|string $cacheKey, mixed $cacheValue, int $ttl = self::TTL): bool {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->put($cacheKey, $cacheValue, $ttl);
        } else {
            return Cache::put($cacheKey, $cacheValue, $ttl);
        }
    }

    /**
     * Remove an item from the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to remove from the cache.
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function forget(UnitEnum|string $cacheKey): bool {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->forget($cacheKey);
        } else {
            return Cache::forget($cacheKey);
        }
    }

    /**
     * Flush all cache entries for the plugin.
     *
     * @return boolean
     *
     * @since 0.0.1
     */
    public static function flush(): bool {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->flush();
        } else {
            return Cache::flush();
        }
    }

    /**
     * Check if a key exists in the cache.
     *
     * @param UnitEnum|string $cacheKey The cache key to check for existence in the cache.
     * @return boolean
     * @since 0.0.1
     */
    public static function has(UnitEnum|string $cacheKey): bool {
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags([self::CACHE_TAG])->has($cacheKey);
        } else {
            return Cache::has($cacheKey);
        }
    }
}
