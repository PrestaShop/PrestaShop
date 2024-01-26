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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Classes;

use PHPUnit\Framework\TestCase;
use Validate;

class ValidateCoreTest extends TestCase
{
    /**
     * @dataProvider isIp2LongDataProvider
     */
    public function testIsIp2Long($expected, $input)
    {
        $this->assertEquals($expected, Validate::isIp2Long($input));
    }

    /**
     * @dataProvider isEmailDataProvider
     */
    public function testIsEmail($expected, $input)
    {
        $this->assertSame($expected, Validate::isEmail($input));
    }

    /**
     * @dataProvider isBirthDateProvider
     */
    public function testIsBirthDate($expected, $input)
    {
        // data from isBirthDateProvider provider are in UTC
        $defaultTz = date_default_timezone_get();
        date_default_timezone_set('UTC');
        try {
            $this->assertSame($expected, Validate::isBirthDate($input));
        } finally {
            date_default_timezone_set($defaultTz);
        }
    }

    /**
     * @dataProvider isDateOrNullProvider
     */
    public function testIsDateOrNull($expected, $input)
    {
        $this->assertSame($expected, Validate::isDateOrNull($input));
    }

    /**
     * @dataProvider isMd5DataProvider
     */
    public function testIsMd5($expected, $input)
    {
        $this->assertSame($expected, Validate::isMd5($input));
    }

    /**
     * @dataProvider isSha1DataProvider
     */
    public function testIsSha1($expected, $input)
    {
        $this->assertSame($expected, Validate::isSha1($input));
    }

    /**
     * @dataProvider isNameDataProvider
     */
    public function testIsName($expected, $input)
    {
        $this->assertSame($expected, Validate::isName($input));
    }

    /**
     * @dataProvider isCustomerNameDataProvider
     */
    public function testIsCustomerName($expected, $input)
    {
        $this->assertSame($expected, Validate::isCustomerName($input));
    }

    /**
     * @dataProvider isFloatDataProvider
     */
    public function testIsFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isFloat($input));
    }

    /**
     * @dataProvider isUnsignedFloatDataProvider
     */
    public function testIsUnsignedFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isUnsignedFloat($input));
    }

    /**
     * @depends testIsFloat
     * @dataProvider isOptFloatDataProvider
     */
    public function testIsOptFloat($expected, $input)
    {
        $this->assertSame($expected, Validate::isOptFloat($input));
    }

    /**
     * @dataProvider isArrayWithIdsDataProvider
     *
     * @param bool $expected
     * @param string|int|array<string|int|bool|array> $input
     */
    public function testIsArrayWithIds(bool $expected, $input)
    {
        $this->assertSame($expected, Validate::isArrayWithIds($input));
    }

    /**
     * @dataProvider isUrlDataProvider
     *
     * @param bool $expected
     * @param string $url
     */
    public function testIsUrl(bool $expected, string $url): void
    {
        $this->assertEquals($expected, Validate::isUrl($url));
    }

    public function isUrlDataProvider(): iterable
    {
        yield 'test quick access link' => [
            true,
            'index.php?controller=AdminCartRules&addcart_rule',
        ];
    }

    public function isIp2LongDataProvider()
    {
        return [
            [false, 'toto'],
            [true, '123'],
        ];
    }

    public function isMd5DataProvider()
    {
        return [
            [1, md5('SomeRandomString')],
            [0, ''],
            [0, sha1('AnotherRandomString')],
            [0, substr(md5('AnotherRandomString'), 0, 31)],
            [0, 123],
            [0, false],
        ];
    }

    public function isSha1DataProvider()
    {
        return [
            [1, sha1('SomeRandomString')],
            [0, ''],
            [0, md5('AnotherRandomString')],
            [0, substr(sha1('AnotherRandomString'), 0, 39)],
            [0, 123],
            [0, false],
        ];
    }

    public function isNameDataProvider()
    {
        return [
            [1, 'Mathieu'],
            [1, 'Dupont'],
            [1, 'Jaçinthé'],
            [1, 'Jaçinthø'],
            [1, 'John D.'],
            [1, 'John D.John'],
            [1, 'John D. John'],
            [1, 'John D. John D.'],
            [1, 'Mario Bros.'],
            [1, 'ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â'],
            [0, 'https://www.website.com'],
            [1, 'www.website.com'],
            [1, 'www\.website\.com'],
            [1, 'www\\.website\\.com'],
            [1, 'www.website.com.'],
            [1, 'website。com'],
            [1, 'John D. www.some.site'],
            [1, 'www.website.com is cool'],
            [1, 'website。com。'],
            [1, 'website。com'],
            [0, 'website%2Ecom'],
            [1, 'website/./com'],
            [1, '.rn'],
            [1, 'websitecom/a'],
            [0, 'websitecom%20a'],
            [1, '`hello'],
            [1, 'hello[my friend]'],
        ];
    }

    public function isCustomerNameDataProvider()
    {
        return [
            [true, 'Mathieu'],
            [true, 'Dupont'],
            [true, 'Jaçinthé'],
            [true, 'Jaçinthø'],
            [true, 'John D.'],
            [true, 'John D. John'],
            [true, 'John D. John D.'],
            [true, 'Mario Bros.'],
            [true, 'ÃƒÆ’Ã‚Â¢ÃƒÂ¢Ã¢â'],
            [false, 'https://www.website.com'],
            [false, 'www.website.com'],
            [false, 'www\.website\.com'],
            [false, 'www\\.website\\.com'],
            [false, 'www.website.com.'],
            [false, 'website。com'],
            [false, 'John D.John'],
            [false, 'John D. www.some.site'],
            [false, 'www.website.com is cool'],
            [false, 'website。com。'],
            [false, 'website。com'],
            [false, 'website%2Ecom'],
            [false, 'website/./com'],
            [false, '.rn'],
            [false, 'websitecom/a'],
            [false, 'websitecom%2falsea'],
            [false, '`hello'],
            [false, 'hello[my friend]'],
        ];
    }

    public function isEmailDataProvider()
    {
        return [
            [true, 'john.doe@prestashop.com'],
            [true, 'john.doe+alias@prestshop.com'],
            [true, 'john.doe+alias@pr.e.sta.shop.com'],
            [true, 'j@p.com'],
            [true, 'john#doe@prestashop.com'],
            [false, ''],
            [false, 'john.doe@prestashop,com'],
            [false, 'john.doe@prestashop'],
            [true, 'john.doe@сайт.рф'],
            [true, 'john.doe@xn--80aswg.xn--p1ai'],
            [false, 'иван@prestashop.com'], // rfc6531 valid but not cyrillic mailer compatible
            [true, 'xn--80adrw@prestashop.com'],
            [true, 'xn--80adrw@xn--80aswg.xn--p1ai'],
            [false, 123456789],
            [false, false],
        ];
    }

    public function isBirthDateProvider()
    {
        return [
            [true, '1991-04-19'],
            [true, '2015-03-22'],
            [true, '1945-07-25'],
            [false, '3000-03-19'],
            [false, '1991-03-33'],
            [false, '1991-15-19'],
            [false, '1801-01-01'],
            [false, '0085-02-25'],
            [true, date('Y-m-d', strtotime('now'))],
            [true, date('Y-m-d', strtotime('-1 day'))],
            [false, date('Y-m-d', strtotime('+1 day'))],
            [true, date('Y-m-d', strtotime('-1 month'))],
            [true, date('Y-m-d', strtotime('-1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 month'))],
            [false, date('Y-m-d', strtotime('+1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 month +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year'))],
            [true, date('Y-m-d', strtotime('-1 year -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year -1 month +1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month -1 day'))],
            [true, date('Y-m-d', strtotime('-1 year +1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year'))],
            [false, date('Y-m-d', strtotime('+1 year -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year -1 month +1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month -1 day'))],
            [false, date('Y-m-d', strtotime('+1 year +1 month +1 day'))],
            [false, date('Y-m-d', strtotime('-201 year'))],
        ];
    }

    public function isDateOrNullProvider()
    {
        return [
            [true, '1991-04-19'],
            [true, '2015-03-22'],
            [true, '1945-07-25'],
            [true, '2020-03-19'],
            [true, '2020-03-19 10:23:00'],
            [true, '2020-03-19 45:99:99'], // Only the date is actually checked
            [false, '1991-03-33'],
            [false, '1991-03-33 00:50:00'],
            [false, '1991-15-19'],
            [true, null],
            [true, '0000-00-00 00:00:00'],
            [true, '0000-00-00'],
        ];
    }

    public function isOptFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [true, -12.2151],
                [true, null],
                [true, ''],
            ]
        );
    }

    public function isUnsignedFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [false, -12.2151],
                [false, -12, 2151],
                [false, '-12.2151'],
                [false, ''],
                [false, 'A'],
                [false, null],
            ]
        );
    }

    public function trueFloatDataProvider()
    {
        return [
            [true, 12],
            [true, 12.2151],
            [true, 12, 2151],
            [true, '12.2151'],
        ];
    }

    public function isFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            [
                [true, -12.2151],
                [true, -12, 2151],
                [true, '-12.2151'],
                [false, ''],
                [false, 'A'],
                [false, null],
            ]
        );
    }

    public function isArrayWithIdsDataProvider(): array
    {
        return [
            [false, 'This is not an array'],
            [false, 42],
            [false, '42'],
            [true, [666]],
            [true, [4, 5, 9, 14]],
            [true, ['2', 5, 4]],
            [true, ['7', '8', '12']],
            [false, []],
            [false, [69, 1, [], 5]],
            [false, [12, 2.5, 14]],
            [false, [-1, 6, 12]],
            [false, ['A', 1, 9]],
            [false, ['+', 666, '+']],
            [false, [0, 2, 253]],
            [false, [0, 0, 0]],
            [false, [45, true, 9]],
        ];
    }
}
