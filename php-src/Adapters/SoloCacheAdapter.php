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

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed|null
     */
    public function get($key, $default = null)
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

    /**
     * @param string $key
     * @param mixed $value
     * @param \DateInterval|int|null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        try {
            return $this->cache->set(strval($this->format->pack($value)));
        } catch (CacheException $ex) {
            return false;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key): bool
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

    /**
     * @param iterable<string|int, string> $keys
     * @param mixed $default
     * @return iterable<string, mixed>
     */
    public function getMultiple($keys, $default = null): iterable
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
    public function setMultiple($values, $ttl = null): bool
    {
        return false;
    }

    /**
     * @param iterable<string|int, string> $keys
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        return $this->clear();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key): bool
    {
        try {
            return $this->cache->exists();
        } catch (CacheException $ex) {
            return false;
        }
    }
}
