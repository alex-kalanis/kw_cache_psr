<?php

namespace kalanis\kw_cache_psr\Adapters;


use DateInterval;
use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use Psr\SimpleCache\CacheInterface;


/**
 * Class MultiCacheAdapter
 * @package kalanis\kw_cache_psr\Adapters
 * Cache adapter for PSR Cache Interface
 */
class MultiCacheAdapter implements CacheInterface
{
    use TCheckKey;

    protected ICache $baseCache;
    protected IFormat $format;
    /** @var array<string, ICache> */
    protected array $caches = [];

    public function __construct(ICache $cache, IFormat $format)
    {
        $this->baseCache = $cache;
        $this->format = $format;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @throws InvalidArgumentException
     * @return mixed|null
     */
    public function get($key, $default = null)
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

    /**
     * @param string $key
     * @param mixed $value
     * @param DateInterval|int|null $ttl
     * @throws InvalidArgumentException
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
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

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @return bool
     */
    public function delete($key): bool
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

    /**
     * @param iterable<string|int, string> $keys
     * @param mixed $default
     * @throws InvalidArgumentException
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
     * @param null|int|DateInterval $ttl
     * @throws InvalidArgumentException
     * @return bool
     */
    public function setMultiple($values, $ttl = null): bool
    {
        $result = true;
        foreach ($values as $key => $value) {
            $result = $result && $this->set($key, $value, $ttl);
        }
        return $result;
    }

    /**
     * @param iterable<string|int, string> $keys
     * @throws InvalidArgumentException
     * @return bool
     */
    public function deleteMultiple($keys): bool
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->delete($key);
        }
        return $result;
    }

    public function has($key): bool
    {
        $usedKey = $this->checkKey($key);
        return isset($this->caches[$usedKey]);
    }
}
