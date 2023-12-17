<?php

namespace kalanis\kw_cache_psr\Adapters;


use Psr\Clock\ClockInterface;


/**
 * Class PoolClock
 * @package kalanis\kw_cache_psr\Adapters
 * Adapter for PSR Clock Interface for possible testing of sub-parts
 */
class PoolClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable();
    }
}
