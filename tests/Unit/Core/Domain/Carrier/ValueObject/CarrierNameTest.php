<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\Carrier\ValueObject;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierName;

class CarrierNameTest extends TestCase
{
    /**
     * @dataProvider getValidNames
     */
    public function testItIsCreatedSuccessfullyWhenValidNameIsGiven($validName)
    {
        new CarrierName($validName);
    }

    public function testItReturnsCorrectValue()
    {
        $carrierName = new CarrierName('my carrier');

        $this->assertEquals('my carrier', $carrierName->getValue());
    }

    /**
     * @dataProvider getInvalidNames
     */
    public function testItThrowsExceptionWhenInvalidNameIsGiven($invalidName)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_CARRIER_NAME);

        new CarrierName($invalidName);
    }

    public function getValidNames()
    {
        yield ['abc'];
        yield ['my carrier'];
        yield ['@mycarrier'];
        yield ['['];
        yield ['long name, but not longer than allowed'];
    }

    public function getInvalidNames()
    {
        yield ['abc#'];
        yield ['>'];
        yield [''];
        yield ['='];
        yield ['{'];
        yield [str_repeat('a', CarrierName::MAX_LENGTH + 1)];
    }
}
