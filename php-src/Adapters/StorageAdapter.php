<?php

namespace kalanis\kw_cache_psr\Adapters;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use kalanis\kw_cache_psr\Traits\TTtl;
use kalanis\kw_storage\Interfaces\IStorage;
use kalanis\kw_storage\StorageException;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;


/**
 * Class StorageAdapter
 * @package kalanis\kw_cache_psr\Adapters
 * Storage adapter for PSR Cache Interface
 */
class StorageAdapter implements CacheInterface
{
    use TCheckKey;
    use TTtl;

    /** @var IStorage */
    protected $storage = null;
    /** @var IFormat */
    protected $format = null;

    public function __construct(IStorage $storage, IFormat $format)
    {
        $this->storage = $storage;
        $this->format = $format;
    }

    public function get(string $key, $default = null)
    {
        try {
            if ($this->has($key)) {
                return $this->format->unpack($this->storage->read($this->checkKey($key)));
            }
            return $default;
        } catch (CacheException | StorageException $ex) {
            return $default;
        }
    }

    public function set(string $key, $value, $ttl = null): bool
    {
        try {
            return $this->storage->write($this->checkKey($key), strval($this->format->pack($value)), $this->timeToInt($ttl));
        } catch (CacheException | StorageException $ex) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        try {
            return $this->storage->remove($this->checkKey($key));
        } catch (StorageException $ex) {
            return false;
        }
    }

    public function clear(): bool
    {
        try {
            $keys = [];
            foreach ($this->storage->lookup('') as $item) {
                $keys[] = $item;
            }
            $result = true;
            foreach ($this->storage->removeMulti($keys) as $key => $status) {
                $result = $result && $status;
            }
            return $result;
        } catch (StorageException $ex) {
            return false;
        }
    }

    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }
        return $result;
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
        foreach ($keys as $item) {
            $result = $result && $this->delete($item);
        }
        return $result;
    }

    public function has(string $key): bool
    {
        try {
            return $this->storage->exists($this->checkKey($key));
        } catch (StorageException $ex) {
            return false;
        }
    }
}
