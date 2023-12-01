<?php

namespace kalanis\kw_cache_psr;


use Exception;
use Psr\SimpleCache\CacheException as ce;


/**
 * Class InvalidArgumentException
 * @package kalanis\kw_cache_psr
 */
class CacheException extends Exception implements ce
{
}
