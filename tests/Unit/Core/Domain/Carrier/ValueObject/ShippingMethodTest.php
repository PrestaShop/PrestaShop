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
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingMethod;

class ShippingMethodTest extends TestCase
{
    /**
     * @dataProvider getUndefinedMethods
     */
    public function testItThrowsAnExceptionWhenUndefinedMethodIsGiven($undefinedMethod)
    {
        $this->expectException(CarrierConstraintException::class);
        $this->expectExceptionCode(CarrierConstraintException::INVALID_SHIPPING_METHOD);

        new ShippingMethod($undefinedMethod);
    }

    public function getUndefinedMethods()
    {
        yield [5];
        yield [100];
        yield [-12];
    }

    public function testItReturnsCorrectMethodValue()
    {
        $shippingMethod = new ShippingMethod(ShippingMethod::SHIPPING_METHOD_PRICE);

        $this->assertEquals(ShippingMethod::SHIPPING_METHOD_PRICE, $shippingMethod->getValue());
    }
}
