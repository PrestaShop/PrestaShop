<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cart\Calculation\CartRules;

use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

/**
 * these tests aim to check the correct calculation of cart total when applying cart rules without code
 *
 * products are inserted as fixtures
 * products are inserted in cart from data providers
 * cart rules are inserted from data providers
 */
class CartRulesWithoutCodeTest extends AbstractCartCalculationTest
{

    /**
     * test bugfix BOOM-5477
     */
    public function testMultipleCartRulesWithoutCode()
    {
        $this->addProductToCart(1,3);
        $this->addProductToCart(2,2);
        $this->addProductToCart(3,1);
        // ad multiple fixtures without code
        $cartRulesData = [
            14 => ['priority' => 12, 'code' => '', 'percent' => 10, 'amount' => 0],
            15 => ['priority' => 13, 'code' => '', 'percent' => 10, 'amount' => 0],
        ];
        foreach ($cartRulesData as $k => $cartRuleData) {
            $this->insertCartRule($k, $cartRuleData);
        }
        $cartRule                = $this->getCartRuleFromFixtureId(1);
        $result                  = $cartRule->checkValidity(\Context::getContext(), false, false);
        $this->assertEquals(true, $result);

        $expectedTotal= (1 - $cartRulesData[14]['percent'] / 100)
                        * (1 - $cartRulesData[15]['percent'] / 100)
                        * (3 * static::PRODUCT_FIXTURES[1]['price']
                           + 2 * static::PRODUCT_FIXTURES[2]['price']
                           + static::PRODUCT_FIXTURES[3]['price'])
                        + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE;
        $this->compareCartTotalTaxIncl($expectedTotal,true);
    }

}
