<?php

namespace AdaptersTests\SimpleCache;


use kalanis\kw_cache\CacheException;
use kalanis\kw_cache\Format\Serialized;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;


/**
 * Class AAdaptersTest
 * @package AdaptersTests\SimpleCache
 * Test things as defined in CacheInterface
 */
abstract class AAdaptersTest extends \CommonTestClass
{
    /**
     * @throws InvalidArgumentException
     */
    public function testNonExistent(): void
    {
        $lib = $this->getCacheInterfacePass();
        $this->assertFalse($lib->has('undefined'));
        $this->assertNull($lib->get('undefined'));
        $this->assertEquals('things', $lib->get('undefined', 'things'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testProcess(): void
    {
        $lib = $this->getCacheInterfacePass();
        $this->assertFalse($lib->has('testing'));
        $this->assertTrue($lib->set('testing', 'something'));
        $this->assertTrue($lib->has('testing'));
        $this->assertEquals('something', $lib->get('testing'));
        $this->assertTrue($lib->set('testing', 'something else'));
        $this->assertEquals('something else', $lib->get('testing'));
        $this->assertTrue($lib->delete('testing'));
        $this->assertFalse($lib->has('testing'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testLarge(): void
    {
        $lib = $this->getCacheInterfacePass();
        $this->assertFalse($lib->has('testing'));
        $this->assertTrue($lib->set('testing', 'something'));
        $this->assertTrue($lib->clear());
        $this->assertFalse($lib->has('testing'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testMultiple(): void
    {
        $lib = $this->getCacheInterfacePass();
        $this->assertNull($lib->get('def'));

        $this->assertTrue($lib->setMultiple([
            'abc' => 'ijn',
            'def' => 'uhb',
            'ghi' => 'zgv',
        ]));
        $this->assertEquals('uhb', $lib->get('def'));

        $this->assertEquals([
            'abc' => 'ijn',
            'ghi' => 'zgv',
            'mno' => null,
        ], $lib->getMultiple(['abc', 'ghi', 'mno']));

        $this->assertTrue($lib->deleteMultiple(['def', 'ghi', 'pqr']));
        $this->assertNull($lib->get('def'));
        $this->assertEquals('ijn', $lib->get('abc'));

        $lib->clear();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testHasFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFailExists();
        $this->assertFalse($lib->has('not need here'));
    }

    /**
     * @throws InvalidArgumentException
     * Must kill the processing via failed unpack - read is necessary to determine if the content is node or not
     */
    public function testGetFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFailUnpack();
        $this->assertTrue($lib->set('some key', 'this will be ignored in this test'));
        $this->assertEquals('this will be passed', $lib->get('some key', 'this will be passed'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testSetFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFailWrite();
        $this->assertFalse($lib->set('not need here', 'this will be ignored'));
    }

    /**
     * @throws InvalidArgumentException
     */
    public function testDeleteFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFail();
        $this->assertFalse($lib->delete('not need here'));
    }

    public function testClearFailStorage(): void
    {
        $lib = $this->getCacheInterfaceFail();
        $this->assertFalse($lib->clear());
    }

    abstract protected function getCacheInterfacePass(): CacheInterface;

    abstract protected function getCacheInterfaceFail(): CacheInterface;

    abstract protected function getCacheInterfaceFailExists(): CacheInterface;

    abstract protected function getCacheInterfaceFailWrite(): CacheInterface;

    abstract protected function getCacheInterfaceFailUnpack(): CacheInterface;
}


class XSerializedFail extends Serialized
{
    public function unpack($content)
    {
        throw new CacheException('mock');
    }
}
