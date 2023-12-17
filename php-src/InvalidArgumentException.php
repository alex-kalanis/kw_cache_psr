<?php

namespace kalanis\kw_cache_psr;


use Psr\Cache\InvalidArgumentException as iae;
use Psr\SimpleCache\InvalidArgumentException as siae;


/**
 * Class InvalidArgumentException
 * @package kalanis\kw_cache_psr
 */
class InvalidArgumentException extends CacheException implements iae, siae
{
}
