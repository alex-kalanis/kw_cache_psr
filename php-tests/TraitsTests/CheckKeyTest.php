<?php

namespace TraitsTests;


use kalanis\kw_cache_psr\InvalidArgumentException;
use kalanis\kw_cache_psr\Traits\TCheckKey;


class CheckKeyTest extends \CommonTestClass
{
    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @dataProvider passProvider
     */
    public function testPass(string $key): void
    {
        $lib = new XCheckKey();
        $this->assertEquals($key, $lib->check($key));
    }

    public function passProvider(): array
    {
        return [
            ['ijn'],
            ['pass here'],
            ['this_is_legal'],
            ['this-is-also-legal'],
            ['Όλυμπος Μύτικας'], // other alphabets
        ];
    }

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @dataProvider failProvider
     */
    public function testFail(string $key): void
    {
        $lib = new XCheckKey();
        $this->expectException(InvalidArgumentException::class);
        $lib->check($key);
    }

    public function failProvider(): array
    {
        return [
            ['fail here 1 {'],
            ['fail here 2 }'],
            ['fail here 3 ('],
            ['fail here 4 )'],
            ['fail here 5 @'],
            ['fail here 6 :'],
            ['fail here 7 /'],
            ['fail here 8 \\'],
        ];
    }
}


class XCheckKey
{
    use TCheckKey;

    /**
     * @param string $key
     * @throws InvalidArgumentException
     * @return string
     */
    public function check(string $key): string
    {
        return $this->checkKey($key);
    }
}
