<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Utils;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Util\ArabicToLatinDigitConverter;
use PrestaShopBundle\Utils\FloatParser;

class FloatParserTest extends TestCase
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
        $this->assertSame($expected, (new FloatParser(new ArabicToLatinDigitConverter()))->fromString($string));
    }

    /**
     * Given a value that is not a string
     * When constructing an ImmutableFloat from that value using ::fromString
     * Then an InvalidArgumentException should be thrown
     *
     * @param mixed $value
     *
     * @dataProvider provideInvalidValues
     */
    public function testItThrowsExceptionIfNotValid($value)
    {
        $this->expectException(\InvalidArgumentException::class);

        (new FloatParser(new ArabicToLatinDigitConverter()))->fromString($value);
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
            // persian
            'persian' => ['۲۰٫۵۰۱۲۳۶۴', 20.5012364],
            // arabic
            'arabic' => ['٢٠٫٥٠١٢٣٦٤', 20.5012364],
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
            [[]],
            [[123]],
            [new \stdClass()],
        ];
    }
}
