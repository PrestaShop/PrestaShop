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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Cart\Calculation\Products;

use PrestaShop\PrestaShop\Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

class CartProductTest extends AbstractCartCalculationTest
{

    /**
     * @dataProvider cartWithoutCartRulesProvider
     */
    public function testCartWithoutCartRules($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    public function cartWithoutCartRulesProvider()
    {
        return array(
            // WITHOUT CART RULES

            'empty cart'                             => array(
                'products'      => array(),
                'expectedTotal' => 0,
                'cartRules'     => array(),
            ),
            'one product in cart, quantity 1'        => array(
                'products'      => array(1 => 1,),
                'expectedTotal' => 19.81,
                'cartRules'     => array(),
            ),
            'one product in cart, quantity 3'        => array(
                'products'      => array(1 => 3,),
                'expectedTotal' => 59.44,
                'cartRules'     => array(),
            ),
            '3 products in cart, several quantities' => array(
                'products'      => array(
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ),
                'expectedTotal' => 155.41,
                'cartRules'     => array(),
            ),
        );
    }
}
