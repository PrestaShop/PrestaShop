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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingRange;

class ShippingRangeTest extends TestCase
{
    /**
     * @dataProvider getValidRangeValues
     */
    public function testItIsCreatedSuccessfullyWhenValidRangeValuesAreGiven($valueFrom, $valueTo)
    {
        new ShippingRange($valueFrom, $valueTo, [3 => 5]);
    }

    /**
     * @dataProvider getInvalidRangeValues
     */
    public function testItThrowsAnExceptionWhenInvalidRangeValuesAreGiven($valueFrom, $valueTo)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_SHIPPING_RANGE);

        new ShippingRange($valueFrom, $valueTo, [3 => 5]);
    }

    public function testItThrowsAnExceptionWhenEmptyPricesByZoneArrayIsGiven()
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_SHIPPING_RANGE);

        new ShippingRange(1, 2, []);
    }

    public function getValidRangeValues()
    {
        yield [1, 2];
        yield [2, 5];
        yield [0, 2];
    }

    public function getInvalidRangeValues()
    {
        yield [2, 2];
        yield [-1, 2];
        yield [3, 1];
        yield [0, 0];
    }
}
