<?php

namespace kalanis\kw_cache_psr\Adapters\PoolCache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Interfaces\IFormat;
use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;


/**
 * Class ItemPoolAdapter
 * @package kalanis\kw_cache_psr\Adapters\PoolCache
 * KwCache pool adapter for PSR Cache Interface
 *
 * OK, kterej debil vymyslel interface pro kombo pool/item? Expire ví storage, ale ne item - volume, redis, memcache.
 * Hit nad itemem závislý na expire je tak blbost, protože tuhle informaci má právě storage a ne samotný item.
 */
class KwPoolAdapter implements CacheItemPoolInterface
{
    use TCheckKey;

    /** @var ICache */
    protected $cache = null;
    /** @var IFormat */
    protected $format = null;
    /** @var CacheItemInterface[] */
    protected $toSave = [];

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

    public function getItems(array $keys = array())
    {
        $result = [];
        foreach ($keys as $key) {
            $useKey = $this->checkKey($key);
            $result[$useKey] = $this->getItem($useKey);
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
        // cannot use this for clearing main cache - you must know all the necessary paths beforehead; so error false
        return false;
    }

    public function deleteItem($key)
    {
        try {
            $useKey = $this->checkKey($key);
            $this->cache->init([$useKey]);
            $this->cache->clear();
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
            $this->cache->init([$useKey]);
            return $this->cache->set($this->format->pack($item->get()));
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
