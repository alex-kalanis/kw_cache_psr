<?php

namespace AdaptersTests\PoolCache;


use kalanis\kw_cache_psr\Adapters\PoolCache\ItemAdapter;
use kalanis\kw_cache_psr\Adapters\PoolCache\MemoryPoolAdapter;
use Psr\Clock\ClockInterface;
use Psr\Cache\InvalidArgumentException;


/**
 * Class MemoryPoolTest
 * @package AdaptersTests\PoolCache
 * Test things as defined in CacheInterface
 */
class MemoryPoolTest extends \CommonTestClass
{
    /**
     * @throws InvalidArgumentException
     */
    public function testProcess(): void
    {
        $lib = new MemoryPoolAdapter();
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

    /**
     * @throws InvalidArgumentException
     */
    public function testInTime(): void
    {
        $data = new ItemAdapter('foo', new XMemoryClock());
        $lib = new MemoryPoolAdapter();
        $this->assertTrue($lib->save($data));

        // time not passed yet
        $data->expiresAt(1578000000);
        $this->assertTrue($lib->hasItem('foo'));

        // time passed
        $data->expiresAt(1577000000);
        $this->assertFalse($lib->hasItem('foo'));

        // time not set - never expire
        $data->expiresAt(null);
        $this->assertTrue($lib->hasItem('foo'));
    }

    public function testFailedSave(): void
    {
        $data = new ItemAdapter('{foo}', new XMemoryClock());
        $lib = new MemoryPoolAdapter();
        $this->assertFalse($lib->saveDeferred($data));
    }
}


class XMemoryClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        // 2020-01-01 00:00:01
        return new \DateTimeImmutable('@1577836801');
    }
}
