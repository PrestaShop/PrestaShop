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
 * these tests aim to check the correct calculation of cart total when applying cart rules
 *
 * products are inserted as fixtures
 * products are inserted in cart from data providers
 * cart rules are inserted from data providers
 */
class CartRulesGiftTest extends AbstractCartCalculationTest
{

    /**
     * @dataProvider cartWithGiftProvider
     */
    public function testCartWithGift(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $expectedProductCount,
        $knownToFailOnV1
    ) {
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->compareCartTotalTaxIncl($expectedTotal, $knownToFailOnV1);
        $this->assertEquals($expectedProductCount, \Cart::getNbProducts($this->cart->id));
    }

    public function cartWithGiftProvider()
    {
        return [
            '1 product in cart (out of stock), 1 cart rule give it as a gift, offering a gift (out of stock) and a global 10% discount' => [
                'products'             => [
                    4 => 1,
                ],
                'expectedTotal'        => 0,
                'cartRules'            => [13],
                'expectedProductCount' => 0,
                'knownToFailOnV1'      => false,
            ],
            '2 products in cart, one cart rule offering a gift (out of stock) and a global 10% discount'                                => [
                'products'             => [
                    1 => 3,
                    4 => 1,
                ],
                'expectedTotal'        => (1 - static::CART_RULES_FIXTURES[13]['percent'] / 100)
                                          * 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [13],
                'oductCount' => 3,
                'knownToFailOnV1'      => true,
            ],
            '2 products in cart, one cart rule offering a gift (in stock) and a global 10% discount'                                    => [
                'products'             => [
                    1 => 2,
                    3 => 3,
                    4 => 1,
                ],
                'expectedTotal'        => (1 - static::CART_RULES_FIXTURES[13]['percent'] / 100)
                                          * (2 * static::PRODUCT_FIXTURES[1]['price']
                                             + 3 * static::PRODUCT_FIXTURES[3]['price']
                                          )
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [12],
                'expectedProductCount' => 6,
                'knownToFailOnV1'      => false,
            ],
        ];
    }
}
