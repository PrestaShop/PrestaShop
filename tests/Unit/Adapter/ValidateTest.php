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

declare(strict_types=1);

namespace Tests\Unit\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Validate;

class ValidateTest extends TestCase
{
    /**
     * @var Validate
     */
    private $validate;

    /**
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->validate = new Validate();
    }

    /**
     * @dataProvider getIsOrderWay
     */
    public function testIsOrderWay(int $expected, $input): void
    {
        self::assertEquals($expected, $this->validate::isOrderWay($input));
    }

    public function getIsOrderWay(): iterable
    {
        yield [0, 'test'];
        yield [0, 1];
        yield [0, true];
        yield [1, 'ASC'];
        yield [1, 'DESC'];
        yield [1, 'asc'];
        yield [1, 'desc'];
        yield [1, 'random'];
        yield [1, 'RANDOM'];
    }

    /**
     * @dataProvider isEmailDataProvider
     */
    public function testIsEmail(bool $expected, string $email): void
    {
        $this->assertSame($expected, $this->validate->isEmail($email));
    }

    public function isEmailDataProvider(): array
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
            [false, 'иван@prestashop.com'], // rfc6531 valid but not swift mailer compatible
            [true, 'xn--80adrw@prestashop.com'],
            [true, 'xn--80adrw@xn--80aswg.xn--p1ai'],
        ];
    }

    /**
     * @param bool $expected
     * @param mixed $value
     *
     * @dataProvider isUnsignedIntProvider
     */
    public function testIsUnsignedInt(bool $expected, $value): void
    {
        self::assertEquals($expected, $this->validate->isUnsignedInt($value));
    }

    public function isUnsignedIntProvider(): array
    {
        return [
            [true, 1],
            [true, 666],
            [true, 0],
            [true, '234'],
            [true, '0'],
            [false, -1],
            [false, '-1'],
            [false, false],
            [false, true],
            [false, null],
            [false, 'invalid'],
            [false, '666invalid'],
        ];
    }

    /**
     * @param bool $expected
     * @param string $objectClassName
     *
     * @dataProvider isValidObjectClassNameDataProvider
     */
    public function testisValidObjectClassName(bool $expected, string $objectClassName): void
    {
        $this->assertSame($expected, $this->validate->isValidObjectClassName($objectClassName));
    }

    /**
     * @param string $html
     * @param bool $iframeAllowed
     * @param $expectedResult
     *
     * @dataProvider isCleanHtmlDataProvider
     *
     * @return void
     */
    public function testIsCleanHtml(string $html, bool $allowFrame, $expectedResult): void
    {
        $this->assertSame($expectedResult, $this->validate->isCleanHtml($html, $allowFrame));
    }

    public function isValidObjectClassNameDataProvider(): array
    {
        return [
            [true, 'MyClassName'],
            [true, '_MyClassName'],
            [true, '_My_Class_Name_'],
            [true, '_MyClassName_'],
            [true, '__My__Class__Name__'],
            [false, ''],
            [false, '666'],
            [true, '_666'],
            [true, '_6_6_6_'],
            [true, '__'],
        ];
    }

    private function isCleanHtmlDataProvider()
    {
        return [
            [
                '<div randomattribute="randomvalue">test</div>', // nominal case
                false,
                true
            ],
            [
                '<div

randomattribute="anything"   attributewithoutvalue

        randomattr="random value">

</div>', // nominal case with added spaces and line jumps
                false,
                true
            ],
            [
                '/form input > embed onerror iframe object', // test plain words with forbidden tag / attributes: should pass
                false,
                true
            ],
            [
                '<a href="#" onchange="evilJavascriptIsCalled()"></a>', // event attributes are forbidden, should not pass
                false,
                false
            ],
            [
                '<a href="#" onanything="evilJavascriptIsCalled()"></a>', // random attribute starting with on should not pass
                false,
                false
            ],
            [
                '<a href="#" oNnotexi="evilJavascriptIsCalled()"></a>', // random attribute starting with on but case insensitive: should not pass
                false,
                false
            ],
            [
                '<iframe src="catvideo.html" /></iframe>', // iframe forbidden
                false,
                false
            ],
            [
                '<iframe src="catvideo.html" /></iframe>', // iframe parameter is set to true, should pass
                true,
                true
            ],
            [
                '<form></form>', // form should not pass,
                false,
                false
            ],
            [
                '<embed></embed>', // embed should not pass
                false,
                false
            ],
            [
                '<input>', // input should not pass
                false,
                false
            ],
            [
                '<script>

    </script>', // script tags are forbidden, should not pass (added a random tabulation and line break
                false,
                false
            ],
            [
                '<object></object>', // objects are forbidden, should not pass
                false,
                false
            ],
            [
                '<div
randomattribute="anything"

    onbidule="test" attributewithoutvalue

        randomattr="random value">test

        </div>', // puting an attribute starting with "on" in the middle of other attributes, with spaces and line breaks: shouldn't pass
                false,
                false
            ],
            [
                '‮<img src=x onerror="alert(\'img\')">', // test RLO xss attack
                false,
                false
            ]
        ];
    }
}
