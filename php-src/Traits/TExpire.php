<?php

namespace kalanis\kw_cache_psr\Traits;


use DateInterval;
use DateTimeInterface;
use Exception;


/**
 * Trait TExpire
 * @package kalanis\kw_cache_psr\Traits
 * Check expiration
 */
trait TExpire
{
    protected ?int $expire = null;

    /**
     * @param object|DateTimeInterface|int|null $expiration
     * @return $this
     */
    public function expiresAt($expiration)
    {
        $when = null;
        if (is_object($expiration) && ($expiration instanceof DateTimeInterface)) {
            $when = $expiration->getTimestamp();
        } elseif (is_int($expiration)) {
            $when = $expiration;
        }
        $this->expire = $when;
        return $this;
    }

    /**
     * @param object|DateInterval|int|null $time
     * @throws Exception
     * @return $this
     */
    public function expiresAfter($time)
    {
        $when = null;
        if (is_object($time) && ($time instanceof DateInterval)) {
            $expiration = new \DateTime();
            $when = $expiration->add($time)->getTimestamp();
        } elseif (is_int($time)) {
            $when = time() + $time;
        }
        $this->expire = $when;
        return $this;
    }

    public function getExpire(): ?int
    {
        return $this->expire;
    }
}
