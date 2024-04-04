<?php

namespace kalanis\kw_cache_psr\Adapters\PoolCache;


use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;


/**
 * Class MemoryPoolAdapter
 * @package kalanis\kw_cache_psr\Adapters\PoolCache
 * Memory pool adapter for PSR Cache Interface
 */
class MemoryPoolAdapter implements CacheItemPoolInterface
{
    use TCheckKey;

    /** @var CacheItemInterface[] */
    protected array $items = [];
    /** @var array<string, CacheItemInterface> */
    protected array $toSave = [];

    public function getItem($key)
    {
        $useKey = $this->checkKey($key);
        $data = $this->hasItem($key) ? $this->items[$useKey] : new ItemAdapter($useKey);
        return $data;
    }

    /**
     * @param array<string> $keys
     * @throws InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @return array<string, CacheItemInterface>|\Traversable<string, CacheItemInterface>
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
        $useKey = $this->checkKey($key);
        return isset($this->items[$useKey]) && $this->items[$useKey]->isHit();
    }

    public function clear()
    {
        $this->items = [];
        $this->toSave = [];
        return true;
    }

    public function deleteItem($key)
    {
        $useKey = $this->checkKey($key);
        if (isset($this->items[$useKey])) {
            unset($this->items[$useKey]);
        }
        return true;
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
            $this->items[$this->checkKey($item->getKey())] = $item;
            return true;
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
