<?php

namespace AdaptersTests\SimpleCache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format\Serialized;
use kalanis\kw_cache\Simple\Variable;
use kalanis\kw_cache_psr\Adapters\SimpleCache\MultiCacheAdapter;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;


class MultiCacheAdapterTest extends AAdaptersTest
{
    /**
     * @throws InvalidArgumentException
     */
    public function testDeleteFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFail();
        $this->assertTrue($lib->delete('not need here'));
    }

    public function testClearFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFail();
        $this->assertTrue($lib->clear());
    }

    protected function getCacheInterfacePass(): CacheInterface
    {
        return new MultiCacheAdapter(new Variable(), new Serialized());
    }

    protected function getCacheInterfaceFail(): CacheInterface
    {
        return new MultiCacheAdapter(new XFailVariable(), new Serialized());
    }

    protected function getCacheInterfaceFailExists(): CacheInterface
    {
        return new MultiCacheAdapter(new XFailVariableExists(), new Serialized());
    }

    protected function getCacheInterfaceFailWrite(): CacheInterface
    {
        return new MultiCacheAdapter(new XFailVariableWrite(), new Serialized());
    }

    protected function getCacheInterfaceFailUnpack(): CacheInterface
    {
        return new MultiCacheAdapter(new Variable(), new XSerializedFail());
    }
}


class XFailVariable extends Variable
{
    public function clear(): void
    {
        throw new CacheException('mock');
    }
}


class XFailVariableExists extends Variable
{
    public function exists(): bool
    {
        throw new CacheException('mock');
    }
}


class XFailVariableWrite extends Variable
{
    public function set(string $content): bool
    {
        throw new CacheException('mock');
    }
}
