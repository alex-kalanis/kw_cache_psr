<?php

namespace TraitsTests;


use DateInterval;
use kalanis\kw_cache_psr\CacheException;
use kalanis\kw_cache_psr\Traits\TTtl;


class TtlTest extends \CommonTestClass
{
    /**
     * @param DateInterval|int|null $key
     * @throws CacheException
     * @dataProvider passProvider
     */
    public function testPass($key): void
    {
        $lib = new XTtl();
        $this->assertEquals($key, $lib->time($key));
    }

    public function passProvider(): array
    {
        return [
            [123456789],
            [null],
        ];
    }

    /**
     * @throws CacheException
     */
    public function testPassInt(): void
    {
        $lib = new XTtl();
        $this->assertNotEmpty($lib->time(new DateInterval('P1D')));
    }

    /**
     * @param DateInterval|int|null $key
     * @throws CacheException
     * @dataProvider failProvider
     */
    public function testFail($key): void
    {
        $lib = new XTtl();
        $this->expectException(CacheException::class);
        $lib->time($key);
    }

    public function failProvider(): array
    {
        return [
            [new \stdClass()],
        ];
    }
}


class XTtl
{
    use TTtl;

    /**
     * @param DateInterval|int|null $time
     * @throws CacheException
     * @return int|null
     */
    public function time($time): ?int
    {
        return $this->timeToInt($time);
    }
}
