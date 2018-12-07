<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\PrestaShopBundle\Utils;

use PrestaShopBundle\Utils\FloatParser;
use PHPUnit\Framework\TestCase;

class FloatParserTest extends TestCase
{
    /**
     * Given a string containing a number with arbitrary characters as thousand and decimal separators
     * When constructing an ImmutableFloat from that string
     * Then the string should be interpreted as a float by ImmutableFloat.
     *
     * @param string $string
     * @param float  $expected
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
     * Then an InvalidArgumentException should be thrown.
     *
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

        return array(
            array('1234567.89', $expected),
            array('1234567,89', $expected),
            array('1,234,567.89', $expected),
            array('1 234 567.89', $expected),
            array('1 234 567,89', $expected),
            array('1,234,567·89', $expected),
            array('1.234.567,89', $expected),
            array('12,34,567.89', $expected),
            array("1'234'567.89", $expected),
            array('1.234.567,89', $expected),
            array('123,4567.89', $expected),
            array('123456789', 123456789.00),
            array('123,456', 123.456),
            array('12,345,678', 12345.678),
            array('9.E-10', 9.E-10),
            array('-1,234,567.89', -1234567.89),
            // persian
            'persian' => array('۲۰٫۵۰۱۲۳۶۴', 20.5012364),
            // arabic
            'arabic' => array('٢٠٫٥٠١٢٣٦٤', 20.5012364),
            // edge cases
            array('1 dot 10', 1.1),
            array('1 hundred and 10 dot 15', 110.15),
            array('1 thousand and 10', 1.1),
            array('', 0.0),
            array('   ', 0.0),
        );
    }

    public function provideInvalidValues()
    {
        return array(
            array('1,'),
            array(',1'),
            array(','),
            array(' , '),
            array('foo'),
            array('1foo'),
            array('0xff'),
            array('minus 10'),
            array(false),
            array(true),
            array(null),
            array(array()),
            array(array(123)),
            array(new \stdClass()),
        );
    }
}
