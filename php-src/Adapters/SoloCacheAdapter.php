<?php

namespace kalanis\kw_cache_psr\Adapters;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Interfaces\IFormat;
use Psr\SimpleCache\CacheInterface;


/**
 * Class SoloCacheAdapter
 * @package kalanis\kw_cache_psr\Adapters
 * Cache adapter for PSR Cache Interface
 * You probably need another one, not this one
 */
class SoloCacheAdapter implements CacheInterface
{
    /** @var ICache */
    protected $cache = null;
    /** @var IFormat */
    protected $format = null;

    public function __construct(ICache $cache, IFormat $format)
    {
        $this->cache = $cache;
        $this->format = $format;
    }

    public function get(string $key, $default = null)
    {
        try {
            if ($this->cache->exists()) {
                return $this->format->unpack($this->cache->get());
            }
            return $default;
        } catch (CacheException $ex) {
            return $default;
        }
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        try {
            return $this->cache->set(strval($this->format->pack($value)));
        } catch (CacheException $ex) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        return $this->clear();
    }

    public function clear(): bool
    {
        try {
            $this->cache->clear();
            return true;
        } catch (CacheException $ex) {
            return false;
        }
    }

    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key, $default);
        }
        return $results;
    }

    /**
     * @param iterable<string, mixed> $values
     * @param null $ttl
     * @return bool
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        return false;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        return $this->clear();
    }

    public function has(string $key): bool
    {
        try {
            return $this->cache->exists();
        } catch (CacheException $ex) {
            return false;
        }
    }
}
