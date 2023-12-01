<?php

namespace kalanis\kw_cache_psr\Traits;


use DateTime;
use DateInterval;
use kalanis\kw_cache_psr\CacheException;


/**
 * Trait TTtl
 * @package kalanis\kw_cache_psr\Traits
 * Check key for problematic characters
 */
trait TTtl
{
    /**
     * @param DateInterval|int|null $time
     * @throws CacheException
     * @return int|null
     */
    protected function timeToInt($time): ?int
    {
        if (is_object($time)) {
            if ($time instanceof DateInterval) {
                $now = new DateTime();
                return $now->add($time)->getTimestamp();
            }
            throw new CacheException('Invalid object for time interval');
        }
        return $time;
    }
}
