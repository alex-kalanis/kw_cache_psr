<?php

namespace AdaptersTests\PoolCache;


use kalanis\kw_cache_psr\Adapters\PoolCache\ItemAdapter;
use Psr\Clock\ClockInterface;


/**
 * Class ItemAdapterTest
 * @package AdaptersTests\PoolCache
 * Test things as defined in CacheInterface
 */
class ItemAdapterTest extends \CommonTestClass
{
    public function testProcess(): void
    {
        $data = new ItemAdapter('foo', new XItemClock());
        $data->set('bar');
        $this->assertEquals('foo', $data->getKey());
    }

    public function testInTime(): void
    {
        $data = new ItemAdapter('foo', new XItemClock());
        $data->set('bar');

        // time not passed yet
        $data->expiresAt(1578000000); // 2020-01-02 21:20
        $this->assertTrue($data->isHit());
        $this->assertEquals('bar', $data->get());

        // time passed
        $data->expiresAt(1577000000); // 2019-12-22 08:33
        $this->assertFalse($data->isHit());
        $this->assertNull($data->get());

        // time not set - never expire
        $data->expiresAt(null);
        $this->assertTrue($data->isHit());
        $this->assertEquals('bar', $data->get());
    }
}


class XItemClock implements ClockInterface
{
    public function now(): \DateTimeImmutable
    {
        // 2020-01-01 00:00:01
        return new \DateTimeImmutable('@1577836801');
    }
}
