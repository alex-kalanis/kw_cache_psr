<?php

namespace AdaptersTests;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format\Serialized;
use kalanis\kw_cache\Simple\Variable;
use kalanis\kw_cache_psr\Adapters\SoloCacheAdapter;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;


class SoloCacheAdapterTest extends AAdaptersTest
{
    /**
     * @throws InvalidArgumentException
     */
    public function testMultiple(): void
    {
        $lib = $this->getCacheInterfacePass();
        $this->assertNull($lib->get('def'));

        $this->assertFalse($lib->setMultiple([
            'abc' => 'ijn',
            'def' => 'uhb',
            'ghi' => 'zgv',
        ]));
        $this->assertEquals(null, $lib->get('def'));

        $this->assertEquals([
            'abc' => null,
            'ghi' => null,
            'mno' => null,
        ], $lib->getMultiple(['abc', 'ghi', 'mno']));

        $this->assertTrue($lib->deleteMultiple(['def', 'ghi', 'pqr']));
        $this->assertNull($lib->get('def'));
        $this->assertEquals(null, $lib->get('abc'));

        $lib->clear();
    }

    protected function getCacheInterfacePass(): CacheInterface
    {
        return new SoloCacheAdapter(new Variable(), new Serialized());
    }

    protected function getCacheInterfaceFail(): CacheInterface
    {
        return new SoloCacheAdapter(new XFailVar(), new Serialized());
    }

    protected function getCacheInterfaceFailExists(): CacheInterface
    {
        return new SoloCacheAdapter(new XFailVarExists(), new Serialized());
    }

    protected function getCacheInterfaceFailWrite(): CacheInterface
    {
        return new SoloCacheAdapter(new XFailVarWrite(), new Serialized());
    }

    protected function getCacheInterfaceFailUnpack(): CacheInterface
    {
        return new SoloCacheAdapter(new Variable(), new XSerializedFail());
    }
}


class XFailVar extends Variable
{
    public function clear(): void
    {
        throw new CacheException('mock');
    }
}


class XFailVarExists extends Variable
{
    public function exists(): bool
    {
        throw new CacheException('mock');
    }
}


class XFailVarWrite extends Variable
{
    public function set(string $content): bool
    {
        throw new CacheException('mock');
    }
}
