<?php

namespace kalanis\kw_cache_psr\Adapters\PoolCache;


use kalanis\kw_cache_psr\Traits\TClock;
use kalanis\kw_cache_psr\Traits\TExpire;
use Psr\Cache\CacheItemInterface;
use Psr\Clock\ClockInterface;


/**
 * Class ItemAdapter
 * @package kalanis\kw_cache_psr\Adapters\PoolCache
 * Item adapter for PSR Cache Interface
 */
class ItemAdapter implements CacheItemInterface
{
    use TClock;
    use TExpire;

    /** @var mixed|null */
    protected $cache = null;
    protected string $key = '';

    public function __construct(string $key, ClockInterface $clock = null)
    {
        $this->key = $key;
        $this->initTClock($clock);
    }

    public function getKey()
    {
        return $this->key;
    }

    public function get()
    {
        return $this->isHit() ? $this->cache : null;
    }

    public function isHit()
    {
        return !$this->isExpired();
    }

    public function set($value)
    {
        $this->cache = $value;
        return $this;
    }

    protected function isExpired(): bool
    {
        $expireTime = $this->getExpire();
        if (is_null($expireTime)) {
            // null = never
            return false;
        }
        return $this->getClocks()->now()->getTimestamp() >= $expireTime;
    }
}
