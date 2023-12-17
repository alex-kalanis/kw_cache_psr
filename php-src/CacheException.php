<?php

namespace kalanis\kw_cache_psr;


use Exception;
use Psr\Cache\CacheException as ce;
use Psr\SimpleCache\CacheException as sce;


/**
 * Class InvalidArgumentException
 * @package kalanis\kw_cache_psr
 */
class CacheException extends Exception implements ce, sce
{
}
