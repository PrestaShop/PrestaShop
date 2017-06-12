<?php

namespace PrestaShop\PrestaShop\Tests\Unit\PrestaShopBundle\Utils;

use PrestaShopBundle\Utils\FloatParser;

class FloatParserTest extends \PHPUnit_Framework_TestCase
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
    public function testItParsesNumbersFromString($string, $expected)
    {
        $this->assertSame($expected, (new FloatParser())->fromString($string));
    }

    /**
     * Given a value that is not a string
     * When constructing an ImmutableFloat from that value using ::fromString
     * Then an InvalidArgumentException should be thrown
     * @param mixed $value
     *
     * @expectedException \InvalidArgumentException
     * @dataProvider provideInvalidValues
     */
    public function testItThrowsExceptionIfNotValid($value)
    {
        (new FloatParser())->fromString($value);
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
            // edge cases
            ['1 dot 10', 1.1],
            ['1 hundred and 10 dot 15', 110.15],
            ['1 thousand and 10', 1.1],
            ['', 0.0],
            ['   ', 0.0],
        ];
    }

    public function provideInvalidValues()
    {
        return [
            ['1,'],
            [',1'],
            [','],
            [' , '],
            ['foo'],
            ['1foo'],
            ['0xff'],
            ['minus 10'],
            [false],
            [true],
            [null],
            [array()],
            [array(123)],
            [new \stdClass()],
        ];
    }
}
