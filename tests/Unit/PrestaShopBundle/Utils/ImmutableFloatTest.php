<?php

namespace PrestaShop\PrestaShop\Tests\Unit\PrestaShopBundle\Utils;

use PrestaShopBundle\Utils\ImmutableFloat;

class ImmutableFloatTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Given a string containing a number with arbitrary characters as thousand and decimal separators
     * When constructing an ImmutableFloat from that string
     * Then the string should be interpreted as a float by ImmutableFloat
     *
     * @param string $string
     * @param float $expected
     *
     * @dataProvider provideValidStrings
     */
    public function testItConvertsNumbersFromString($string, $expected)
    {
        $instance = ImmutableFloat::fromString($string);
        $this->assertSame($expected, $instance->getValue());
    }

    public function provideValidStrings()
    {
        $expected = 1234567.89;
        return [
            ['1234567.89', $expected],
            ['1234567,89', $expected],
            ['1,234,567.89', $expected],
            ['1 234 567.89', $expected],
            ['1 234 567,89', $expected],
            ['1,234,567·89', $expected],
            ['1.234.567,89', $expected],
            ['12,34,567.89', $expected],
            ["1'234'567.89", $expected],
            ['1.234.567,89', $expected],
            ['123,4567.89', $expected],
            ['123456789', 123456789.00],
            ['123,456', 123.456],
            ['12,345,678', 12345.678],
            ['9.E-10', 9.E-10],
            ['-1,234,567.89', -1234567.89],
            ['', 0.0],
        ];
    }
}
