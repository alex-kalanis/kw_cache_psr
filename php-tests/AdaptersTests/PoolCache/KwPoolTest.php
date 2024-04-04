<?php

namespace AdaptersTests\PoolCache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format\Serialized;
use kalanis\kw_cache\Interfaces\ICache;
use kalanis\kw_cache\Storage\Basic;
use kalanis\kw_cache_psr\Adapters\PoolCache\ItemAdapter;
use kalanis\kw_cache_psr\Adapters\PoolCache\KwPoolAdapter;
use kalanis\kw_cache_psr\Adapters\PoolClock;
use kalanis\kw_storage\Storage\Key\DefaultKey;
use kalanis\kw_storage\Storage\Storage;
use kalanis\kw_storage\Storage\Target\Memory;
use Psr\Clock\ClockInterface;
use Psr\Cache\InvalidArgumentException;


/**
 * Class KwPoolTest
 * @package AdaptersTests\PoolCache
 * Test things as defined in CacheInterface
 */
class KwPoolTest extends \CommonTestClass
{
    /**
     * @throws InvalidArgumentException
     */
    public function testProcess(): void
    {
        $lib = $this->getLib();
        $this->assertTrue($lib->commit());
        $this->assertEmpty($lib->getItems());
        $this->assertTrue($lib->save(new ItemAdapter('foo')));
        $this->assertTrue($lib->saveDeferred(new ItemAdapter('bar')));
        $this->assertFalse($lib->hasItem('bar'));
        $this->assertTrue($lib->commit());
        $this->assertTrue($lib->hasItem('foo'));
        $this->assertNotEmpty($lib->getItem('foo'));
        $this->assertTrue($lib->deleteItems(['foo']));
        $this->assertFalse($lib->hasItem('foo'));
        $this->assertTrue($lib->hasItem('bar'));
        $this->assertNotEmpty($lib->getItems(['bar', 'baz']));
        $this->assertTrue($lib->clear());
        $this->assertFalse($lib->hasItem('bar'));
        $this->assertEmpty($lib->getItems(['bar', 'baz']));

        $this->assertFalse($lib->save(new ItemAdapter('{foo}')));
    }

    public function testFailedSave(): void
    {
        $data = new ItemAdapter('{foo}', new XMemoryClock());
        $lib = $this->getLib();
        $this->assertFalse($lib->saveDeferred($data));
    }

    public function testClock(): void
    {
        $lib = new PoolClock();
        $this->assertNotEmpty($lib->now()->getTimestamp());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testGetItemFail(): void
    {
        $this->assertNull($this->getFailedLib()->getItem('foo')->get());
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testHasItemFail(): void
    {
        $this->assertFalse($this->getFailedLib()->hasItem('foo'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testDeleteFail(): void
    {
        $this->assertFalse($this->getFailedLib()->deleteItem('foo'));
    }

    public function testClearFail(): void
    {
        $this->assertFalse($this->getFailedLib()->addKey('{foo}')->clear());
    }

    public function testSaveFail(): void
    {
        $this->assertFalse($this->getFailedLib()->save(new ItemAdapter('foo')));
    }

    public function testSaveFail2(): void
    {
        $this->assertFalse($this->getFailedLib()->save(new ItemAdapter('{foo}')));
    }

    protected function getLib(): KwPoolAdapter
    {
        return new KwPoolAdapter(
            new Basic( // must to use regular storage - memory on simple storage is not enough (does not store more entries)
                new Storage(
                    new DefaultKey(),
                    new Memory()
                )
            ), new Serialized()
        );
    }

    protected function getFailedLib(): XPoolAdapter
    {
        return new XPoolAdapter(new XFailBasic(), new Serialized());
    }
}


class XPoolClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        // 2020-01-01 00:00:01
        return new \DateTimeImmutable('@1577836801');
    }
}


class XPoolAdapter extends KwPoolAdapter
{
    public function addKey(string $key): self
    {
        $this->keys[$key] = $key;
        return $this;
    }
}


class XFailBasic implements ICache
{
    public function init(array $what): void
    {
        throw new CacheException('mock');
    }

    public function exists(): bool
    {
        throw new CacheException('mock');
    }

    public function set(string $content): bool
    {
        throw new CacheException('mock');
    }

    public function get(): string
    {
        throw new CacheException('mock');
    }

    public function clear(): void
    {
        throw new CacheException('mock');
    }
}
