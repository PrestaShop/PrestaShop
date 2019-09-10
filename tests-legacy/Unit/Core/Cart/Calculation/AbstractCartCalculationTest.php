<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace LegacyTests\Unit\Core\Cart\Calculation;

use Cart;
use LegacyTests\Unit\Core\Cart\AbstractCartTest;

/**
 * these tests aim to check the correct calculation of cart total
 */
abstract class AbstractCartCalculationTest extends AbstractCartTest
{
    protected function compareCartTotalTaxIncl($expectedTotal, $knownToFailOnV1 = false)
    {
        $carrierId = (int) $this->cart->id_carrier <= 0 ? null : $this->cart->id_carrier;
        $totalV1   = $this->cart->getOrderTotalV1(true, Cart::BOTH, null, $carrierId);
        $totalV2   = $this->cart->getOrderTotal(true, Cart::BOTH, null, $carrierId);
        // here we round values to avoid round issues : rounding modes are tested by specific tests
        $expectedTotal = round($expectedTotal, 1);
        $totalV1       = round($totalV1, 1);
        if (!$knownToFailOnV1) {
            $this->assertEquals($expectedTotal, $totalV1, 'V1 fail (tax incl)');
        }
        $totalV2 = round($totalV2, 1);
        $this->assertEquals($expectedTotal, $totalV2, 'V2 fail (tax incl)');
    }

    protected function compareCartTotalTaxExcl($expectedTotal, $knownToFailOnV1 = false)
    {
        $carrierId = (int) $this->cart->id_carrier <= 0 ? null : $this->cart->id_carrier;
        $totalV1   = $this->cart->getOrderTotalV1(false, Cart::BOTH, null, $carrierId);
        $totalV2   = $this->cart->getOrderTotal(false, Cart::BOTH, null, $carrierId);
        // here we round values to avoid round issues : rounding modes are tested by specific tests
        $expectedTotal = round($expectedTotal, 1);
        $totalV1       = round($totalV1, 1);
        if (!$knownToFailOnV1) {
            $this->assertEquals($expectedTotal, $totalV1, 'V1 fail (tax excl)');
        }
        $totalV2 = round($totalV2, 1);
        $this->assertEquals($expectedTotal, $totalV2, 'V2 fail (tax excl)');
    }
}
