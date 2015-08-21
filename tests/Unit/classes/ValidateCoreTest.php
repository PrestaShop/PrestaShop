<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PHPUnit_Framework_TestCase;
use Validate;

class ValidateCoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider isIp2LongDataProvider
     */
    public function testIsIp2Long($expected, $input)
    {
        $this->assertEquals($expected, Validate::isIp2Long($input));
    }

    public function testIsAnything()
    {
        $this->assertTrue(Validate::isAnything());
    }

    // TODO: Write test for testIsModuleUrl()
    public function testIsModuleUrl()
    {
        //$this->assertSame($expected, Validate::isEmail($input));
    }

    /**
     * @dataProvider isEmailDataProvider
     */
    public function testIsEmail($expected, $input)
    {
        $this->assertSame($expected, Validate::isEmail($input));
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
        
        // --- providers ---

        public function isIp2LongDataProvider()
        {
            return array(
            array(false, 'toto'),
            array(true, '123')
        );
        }

    public function isMd5DataProvider()
    {
        return array(
            array(1, md5('SomeRandomString')),
            array(0, ''),
            array(0, sha1('AnotherRandomString')),
            array(0, substr(md5('AnotherRandomString'), 0, 31)),
            array(0, 123),
            array(0, false),
        );
    }

    public function isSha1DataProvider()
    {
        return array(
            array(1, sha1('SomeRandomString')),
            array(0, ''),
            array(0, md5('AnotherRandomString')),
            array(0, substr(sha1('AnotherRandomString'), 0, 39)),
            array(0, 123),
            array(0, false),
        );
    }

    public function isEmailDataProvider()
    {
        return array(
            array(true, 'john.doe@prestashop.com'),
            array(true, 'john.doe+alias@prestshop.com'),
            array(true, 'john.doe+alias@pr.e.sta.shop.com'),
            array(true, 'j@p.com'),
            array(true, 'john#doe@prestashop.com'),
            array(false, ''),
            array(false, 'john.doe@prestashop,com'),
            array(false, 'john.doe@prestashop'),
            array(false, 123456789),
            array(false, false),
        );
    }

    public function isOptFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            array(
                array(true, -12.2151),
                array(true, null),
                array(true, ''),
            )
        );
    }

    public function isUnsignedFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            array(
                array(false, -12.2151),
                array(false, -12,2151),
                array(false, '-12.2151'),
                array(false, ''),
                array(false, 'A'),
                array(false, null),
            )
        );
    }

    public function trueFloatDataProvider()
    {
        return array(
            array(true, 12),
            array(true, 12.2151),
            array(true, 12,2151),
            array(true, '12.2151'),
        );
    }

    public function isFloatDataProvider()
    {
        return array_merge(
            $this->trueFloatDataProvider(),
            array(
                array(true, -12.2151),
                array(true, -12,2151),
                array(true, '-12.2151'),
                array(false, ''),
                array(false, 'A'),
                array(false, null),
            )
        );
    }
}
