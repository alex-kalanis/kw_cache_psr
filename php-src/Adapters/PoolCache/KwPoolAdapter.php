<?php

namespace kalanis\kw_cache_psr\Adapters\PoolCache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Traversable;


/**
 * Class ItemPoolAdapter
 * @package kalanis\kw_cache_psr\Adapters\PoolCache
 * KwCache pool adapter for PSR Cache Interface
 *
 * OK, what kind of idiot has that brilliant idea about pool/item combo? Expire is known by storage, but not by item - volume. redis, memcache.
 * The Hit over item dependent on expire is also dumb, because that information is on storage, not that item itself.
 */
class KwPoolAdapter implements CacheItemPoolInterface
{
    use TCheckKey;

    protected ICache $cache;
    protected IFormat $format;
    /** @var array<string, CacheItemInterface> */
    protected array $toSave = [];
    /** @var array<string> Only known to the run, others will stay! */
    protected array $keys = [];

    public function __construct(ICache $cache, IFormat $format)
    {
        $this->cache = $cache;
        $this->format = $format;
    }

    public function getItem($key)
    {
        try {
            $useKey = $this->checkKey($key);
            $this->cache->init([$useKey]);
            $data = new ItemAdapter($useKey);
            $data->set($this->format->unpack($this->cache->get()));
            return $data;
        } catch (CacheException $ex) {
            return new ItemAdapter($useKey);
        }
    }

    /**
     * @param array<string> $keys
     * @throws InvalidArgumentException
     * @-throws \Psr\Cache\InvalidArgumentException
     * @return array<string, CacheItemInterface>|Traversable<string, CacheItemInterface>
     */
    public function getItems(array $keys = array())
    {
        $result = [];
        foreach ($keys as $key) {
            if ($this->hasItem($key)) {
                $useKey = $this->checkKey($key);
                $result[$useKey] = $this->getItem($useKey);
            }
        }
        return $result;
    }

    public function hasItem($key)
    {
        try {
            $useKey = $this->checkKey($key);
            $this->cache->init([$useKey]);
            return $this->cache->exists();
        } catch (CacheException $ex) {
            return false;
        }
    }

    public function clear()
    {
        // clear things which can be saved later
        $this->toSave = [];
        try {
            return $this->deleteItems($this->keys);
        } catch (\Psr\Cache\InvalidArgumentException $e) {
            return false;
        }
    }

    public function deleteItem($key)
    {
        try {
            $useKey = $this->checkKey($key);
            $this->cache->init([$useKey]);
            $this->cache->clear();
            unset($this->keys[$useKey]);
            return true;
        } catch (CacheException $ex) {
            return false;
        }
    }

    public function deleteItems(array $keys)
    {
        $result = true;
        foreach ($keys as $key) {
            $result = $result && $this->deleteItem($key);
        }
        return $result;
    }

    public function save(CacheItemInterface $item)
    {
        try {
            $useKey = $this->checkKey($item->getKey());
            $this->keys[$useKey] = $useKey;
            $this->cache->init([$useKey]);
            return $this->cache->set(strval($this->format->pack($item->get())));
        } catch (CacheException $ex) {
            return false;
        } catch (InvalidArgumentException $ex) {
            return false;
        }
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        try {
            $this->toSave[$this->checkKey($item->getKey())] = $item;
            return true;
        } catch (InvalidArgumentException $ex) {
            return false;
        }
    }

    public function commit()
    {
        $result = true;
        foreach ($this->toSave as $item) {
            $result = $result && $this->save($item);
        }
        return $result;
    }
}
