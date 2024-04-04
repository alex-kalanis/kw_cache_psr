<?php

namespace TraitsTests;


use kalanis\kw_cache_psr\Traits\TExpire;


/**
 * Class ExpireTest
 * @package TraitsTests
 * Test expiration
 */
class ExpireTest extends \CommonTestClass
{
    public function testAt(): void
    {
        $lib = new XExpire();
        $lib->expiresAt(null);
        $this->assertNull($lib->getExpire());
        $lib->expiresAt(1578000000);
        $this->assertEquals(1578000000, $lib->getExpire());
        $lib->expiresAt(new \DateTimeImmutable('@1577000000'));
        $this->assertEquals(1577000000, $lib->getExpire());
    }

    /**
     * @throws \Exception
     */
    public function testAfter(): void
    {
        $lib = new XExpire();
        $lib->expiresAfter(null);
        $this->assertNull($lib->getExpire());

        $time = 2000;
        $lib->expiresAfter($time);
        $this->assertEquals(time() + $time, $lib->getExpire());

        $interval = new \DateInterval('P1D');
        $expiration = new \DateTime();
        $lib->expiresAfter($interval);
        $this->assertEquals($expiration->add($interval)->getTimestamp(), $lib->getExpire());
    }
}


class XExpire
{
    use TExpire;
}
