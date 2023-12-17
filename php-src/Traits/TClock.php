<?php

namespace kalanis\kw_cache_psr\Traits;


use kalanis\kw_cache_psr\Adapters\PoolClock;
use Psr\Clock\ClockInterface;


/**
 * Trait TClock
 * @package kalanis\kw_cache_psr\Traits
 * Process clocks
 */
trait TClock
{
    /** @var ClockInterface|null */
    protected $clock = null;

    public function initTClock(?ClockInterface $clock): void
    {
        $this->clock = $clock;
    }

    public function getClocks(): ClockInterface
    {
        return $this->clock ?: new PoolClock();
    }
}
