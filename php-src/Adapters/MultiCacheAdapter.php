<?php

namespace kalanis\kw_cache_psr\Adapters;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;


/**
 * Class MultiCacheAdapter
 * @package kalanis\kw_cache_psr\Adapters
 * Cache adapter for PSR Cache Interface
 */
class MultiCacheAdapter implements CacheInterface
{
    use TCheckKey;

    /** @var ICache */
    protected $baseCache = null;
    /** @var array<string, ICache> */
    protected $caches = null;
    /** @var IFormat */
    protected $format = null;

    public function __construct(ICache $cache, IFormat $format)
    {
        $this->baseCache = $cache;
        $this->format = $format;
    }

    public function get(string $key, $default = null)
    {
        try {
            $usedKey = $this->checkKey($key);
            if (isset($this->caches[$usedKey])) {
                return $this->format->unpack($this->caches[$usedKey]->get());
            }
            return $default;
        } catch (CacheException $ex) {
            return $default;
        }
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        try {
            $usedKey = $this->checkKey($key);
            if (!isset($this->caches[$usedKey])) {
                $cache = clone $this->baseCache;
                $cache->init([$usedKey]);
            } else {
                $cache = $this->caches[$usedKey];
            }
            $cache->set(strval($this->format->pack($value)));
            $this->caches[$usedKey] = $cache;
            return true;
        } catch (CacheException $ex) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        $usedKey = $this->checkKey($key);
        if (isset($this->caches[$usedKey])) {
            unset($this->caches[$usedKey]);
        }
        return true;
    }

    public function clear(): bool
    {
        $this->caches = [];
        return true;
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
     * @param null|int|\DateInterval $ttl
     * @throws InvalidArgumentException
     * @return bool
     */
    public function setMultiple(iterable $values, $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    public function deleteMultiple(iterable $keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->delete($key);
        }
        return $result;
    }

    public function has(string $key): bool
    {
        $usedKey = $this->checkKey($key);
        return isset($this->caches[$usedKey]);
    }
}
