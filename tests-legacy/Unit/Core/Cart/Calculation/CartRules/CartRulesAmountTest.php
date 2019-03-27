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

namespace LegacyTests\Unit\Core\Cart\Calculation\CartRules;

use LegacyTests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

/**
 * behat equivalent : Scenarii/Cart/Calculation/CartRule/amount_*.feature
 */
class CartRulesAmountTest extends AbstractCartCalculationTest
{
    /**
     * @dataProvider cartWithOneCartRuleAmountProvider
     */
    public function testCartAmountWithOneCartRule(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $knownToFailOnV1
    ) {
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->compareCartTotalTaxIncl($expectedTotal, $knownToFailOnV1);
    }

    /**
     * @dataProvider cartWithMultipleCartRulesAmountProvider
     */
    public function testCartAmountWithMultipleCartRules(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $knownToFailOnV1
    ) {
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->compareCartTotalTaxIncl($expectedTotal, $knownToFailOnV1);
    }

    public function cartWithOneCartRuleAmountProvider()
    {
        return [
            'empty cart'                                                                                      => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [4],
                'knownToFailOnV1'=>false,
            ],
            'one product in cart, quantity 1, one 5€ global voucher'                                          => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => static::PRODUCT_FIXTURES[1]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4],
                'knownToFailOnV1'=>false,
            ],
            'one product in cart, quantity 1, one 500€ global voucher'                                        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE, // voucher exceeds total
                'cartRules'     => [5],
                'knownToFailOnV1'=>false,
            ],
            'one product in cart, quantity 3, one 5€ global voucher'                                          => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 3 * static::PRODUCT_FIXTURES[1]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4],
                'knownToFailOnV1'=>false,
            ],
            '3 products in cart, several quantities, one 5€ global voucher (reduced product at first place)'  => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 3 * static::PRODUCT_FIXTURES[1]['price']
                + 2 * static::PRODUCT_FIXTURES[2]['price']
                + static::PRODUCT_FIXTURES[3]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4],
                'knownToFailOnV1'=>false,
            ],
            '3 products in cart, several quantities, one 5€ global voucher (reduced product at second place)' => [
                'products'      => [
                    1 => 3, // 59.43
                    2 => 2, // 64.776
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 3 * static::PRODUCT_FIXTURES[1]['price']
                + 2 * static::PRODUCT_FIXTURES[2]['price']
                + static::PRODUCT_FIXTURES[3]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4],
                'knownToFailOnV1'=>false,
            ],
            '3 products in cart, several quantities, one 500€ global voucher'                                 => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE, // voucher exceeds total
                'cartRules'     => [5],
                'knownToFailOnV1'=>false,
            ],
        ];
    }

    public function cartWithMultipleCartRulesAmountProvider()
    {
        return [
            'empty cart'                                                                            => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [4, 6],
                'knownToFailOnV1'=>false,
            ],
            'one product in cart, quantity 1, one 5€ global voucher, one 10€ global voucher'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => static::PRODUCT_FIXTURES[1]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                - static::CART_RULES_FIXTURES[6]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4, 6],
                'knownToFailOnV1'=>false,
            ],
            'one product in cart, quantity 3, one 5€ global voucher, one 10€ global voucher'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 3 * static::PRODUCT_FIXTURES[1]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                - static::CART_RULES_FIXTURES[6]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4, 6],
                'knownToFailOnV1'=>false,
            ],
            '3 products in cart, several quantities, one 5€ global voucher, one 10€ global voucher' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 3 * static::PRODUCT_FIXTURES[1]['price']
                + 2 * static::PRODUCT_FIXTURES[2]['price']
                + static::PRODUCT_FIXTURES[3]['price']
                - static::CART_RULES_FIXTURES[4]['amount']
                - static::CART_RULES_FIXTURES[6]['amount']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [4, 6],
                'knownToFailOnV1'=>false,
            ],
        ];
    }
}
