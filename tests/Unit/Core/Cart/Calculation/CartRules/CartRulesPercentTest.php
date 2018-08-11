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
class CartRulesPercentTest extends AbstractCartCalculationTest
{

    /**
     * @dataProvider cartWithOneCartRulePercentProvider
     */
    public function testCartWithOneCartRulePercent(
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
     * @dataProvider cartWithMultipleCartRulesPercentProvider
     */
    public function testCartWithMultipleCartRulesPercent(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $knownToFailOnV1
    ) {
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->compareCartTotalTaxIncl($expectedTotal, $knownToFailOnV1);
    }

    public function cartWithOneCartRulePercentProvider()
    {
        return [
            'empty cart'                                                     => [
                'products'        => [],
                'expectedTotal'   => 0,
                'cartRules'       => [2],
                'knownToFailOnV1' => false,
            ],
            'one product in cart, quantity 1, one 50% global voucher'        => [
                'products'        => [
                    1 => 1,
                ],
                'expectedTotal'   => (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                     * static::PRODUCT_FIXTURES[1]['price']
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2],
                'knownToFailOnV1' => false,
            ],
            'one product in cart, quantity 3, one 50% global voucher'        => [
                'products'        => [
                    1 => 3,
                ],
                'expectedTotal'   => (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                     * 3 * static::PRODUCT_FIXTURES[1]['price']
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2],
                'knownToFailOnV1' => false,
            ],
            '3 products in cart, several quantities, one 50% global voucher' => [
                'products'        => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal'   => (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                     * (3 * static::PRODUCT_FIXTURES[1]['price']
                                        + 2 * static::PRODUCT_FIXTURES[2]['price']
                                        + static::PRODUCT_FIXTURES[3]['price'])
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2],
                'knownToFailOnV1' => false,
            ],
        ];
    }

    public function cartWithMultipleCartRulesPercentProvider()
    {
        return [
            'empty cart'                                                   => [
                'products'        => [],
                'expectedTotal'   => 0,
                'cartRules'       => [2, 3],
                'knownToFailOnV1' => false,
            ],
            'one product in cart, quantity 1, 2x % global vouchers'        => [
                'products'        => [
                    1 => 1,
                ],
                'expectedTotal'   => (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                     * (1 - static::CART_RULES_FIXTURES[3]['percent'] / 100)
                                     * static::PRODUCT_FIXTURES[1]['price']
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2, 3],
                'knownToFailOnV1' => true,
            ],
            'one product in cart, quantity 3, 2x % global vouchers'        => [
                'products'        => [
                    1 => 3,
                ],
                'expectedTotal'   => round(
                                         (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                         * (1 - static::CART_RULES_FIXTURES[3]['percent'] / 100)
                                         * 3 * static::PRODUCT_FIXTURES[1]['price'],
                                         2
                                     )
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2, 3],
                'knownToFailOnV1' => true,
            ],
            '3 products in cart, several quantities, 2x % global vouchers' => [
                'products'        => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal'   => (1 - static::CART_RULES_FIXTURES[2]['percent'] / 100)
                                     * (1 - static::CART_RULES_FIXTURES[3]['percent'] / 100)
                                     * (3 * static::PRODUCT_FIXTURES[1]['price']
                                        + 2 * static::PRODUCT_FIXTURES[2]['price']
                                        + static::PRODUCT_FIXTURES[3]['price']
                                     )
                                     + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'       => [2, 3],
                'knownToFailOnV1' => true,
            ],
        ];
    }
}
