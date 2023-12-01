<?php

namespace kalanis\kw_cache_psr\Traits;


use kalanis\kw_cache_psr\InvalidArgumentException;


/**
 * Trait TCheckKey
 * @package kalanis\kw_cache_psr\Traits
 * Check key for problematic characters
 */
trait TCheckKey
{
    /**
     * @param string $key
     * @return string
     * @throws InvalidArgumentException
     */
    protected function checkKey(string $key): string
    {
        // problematic chars
        // {}()/\@:
        if (false !== strpos($key, '{')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "{"', $key));
        }
        if (false !== strpos($key, '}')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "}"', $key));
        }
        if (false !== strpos($key, '(')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "("', $key));
        }
        if (false !== strpos($key, ')')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains ")"', $key));
        }
        if (false !== strpos($key, '/')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "/"', $key));
        }
        if (false !== strpos($key, '\\')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "\\"', $key));
        }
        if (false !== strpos($key, '@')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains "@"', $key));
        }
        if (false !== strpos($key, ':')) {
            throw new InvalidArgumentException(sprintf('The key *%s* contains ":"', $key));
        }
        return $key;
    }
}
