<?php

namespace kalanis\kw_cache_psr;


use Psr\SimpleCache\InvalidArgumentException as iae;


/**
 * Class InvalidArgumentException
 * @package kalanis\kw_cache_psr
 */
class InvalidArgumentException extends CacheException implements iae
{
}
