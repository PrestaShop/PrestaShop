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

use PHPUnit\Framework\TestCase;

class VersionNumberTest extends TestCase
{
    public function testConstruct()
    {
        $number = new VersionNumber(1.7, 2, 3);

        $this->assertEquals(1.7, $number->getMajor());
        $this->assertEquals(2, $number->getMinor());
        $this->assertEquals(3, $number->getPatch());
    }

    public function testToString()
    {
        $number = new VersionNumber(1.6, 1, 21);

        $this->assertEquals('1.6.1.21', $number->__toString());
    }

    public function testFromStringGoodUsecases()
    {
        $this->assertEquals(
            '1.6.1.20',
            VersionNumber::fromString('1.6.1.20')->__toString()
        );
        $this->assertEquals(
            '1.7.0.0',
            VersionNumber::fromString('1.7.0.0')->__toString()
        );
        $this->assertEquals(
            '1.8.4.2',
            VersionNumber::fromString('1.8.4.2')->__toString()
        );
    }

    public function testFromStringBadUsecases()
    {
        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1.2.3.4.5');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('1.2');

        $this->expectException(\InvalidArgumentException::class);
        $number = VersionNumber::fromString('a');
    }

    public function testCompareSameVersions()
    {
        $number1 = new VersionNumber(1.6, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(0, $number1->compare($number2));
    }

    public function testCompareHigher()
    {
        $number1 = new VersionNumber(1.7, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(1, $number1->compare($number2));

        $number3 = new VersionNumber(1.8, 2, 3);
        $number4 = new VersionNumber(1.8, 1, 3);

        $this->assertEquals(1, $number3->compare($number4));

        $number5 = new VersionNumber(1.8, 2, 3);
        $number6 = new VersionNumber(1.8, 2, 1);

        $this->assertEquals(1, $number5->compare($number6));
    }

    public function testCompareLower()
    {
        $number1 = new VersionNumber(1.5, 2, 3);
        $number2 = new VersionNumber(1.6, 2, 3);

        $this->assertEquals(-1, $number1->compare($number2));

        $number3 = new VersionNumber(1.8, 2, 3);
        $number4 = new VersionNumber(1.8, 5, 3);

        $this->assertEquals(-1, $number3->compare($number4));

        $number5 = new VersionNumber(1.8, 2, 3);
        $number6 = new VersionNumber(1.8, 2, 7);

        $this->assertEquals(-1, $number5->compare($number6));
    }
}
