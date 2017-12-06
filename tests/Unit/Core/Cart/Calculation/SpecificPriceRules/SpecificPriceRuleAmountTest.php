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

namespace Tests\Unit\Core\Cart\Calculation\SpecificPriceRules;

class SpecificPriceRuleAmountTest extends AbstractSpecificPriceRuleTest
{

    const SPECIFIC_PRICE_RULES_FIXTURES = [
        1 => ['reductionType' => 'amount', 'reduction' => 1, 'fromQuantity' => 1],
        2 => ['reductionType' => 'amount', 'reduction' => 3, 'fromQuantity' => 2],
        3 => ['reductionType' => 'amount', 'reduction' => 300, 'fromQuantity' => 2],
    ];

    /**
     * @dataProvider specificPriceRuleAmountProvider
     *
     * @param $productData
     * @param $expectedTotal
     * @param $cartRuleData
     * @param $specificCartRuleData
     *
     * @throws \Exception
     */
    public function testSpecificPriceRuleAmount(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $specificCartRuleData
    ) {
        $this->resetCart();
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        foreach ($specificCartRuleData as $specificCartRuleId) {
            $this->insertSpecificPriceRule($specificCartRuleId);
        }

        $this->compareCartTotalTaxIncl($expectedTotal);
    }

    public function specificPriceRuleAmountProvider()
    {
        return [
            '1 product in cart, quantity 1, one rule amount from quantity 1'                                  => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                                          - static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '1 product in cart, quantity 1, one rule amount from quantity 2'                                  => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
            '1 product in cart, quantity 3, one rule amount from quantity 1'                                  => [
                'products'             => [
                    1 => 3,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          - 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '1 product in cart, quantity 3, one rule amount from quantity 2'                                  => [
                'products'             => [
                    1 => 3,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          - 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[2]['reduction']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
            '3 products in cart, several quantities, one rule amount from quantity 1'                         => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          - 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction']
                                          + 2 * static::PRODUCT_FIXTURES[2]['price']
                                          - 2 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction']
                                          + static::PRODUCT_FIXTURES[3]['price']
                                          - static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '3 products in cart, several quantities, one rule amount from quantity 2'                         => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          - 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[2]['reduction']
                                          + 2 * static::PRODUCT_FIXTURES[2]['price']
                                          - 2 * static::SPECIFIC_PRICE_RULES_FIXTURES[2]['reduction']
                                          + static::PRODUCT_FIXTURES[3]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
            '3 products in cart, several quantities, one rule amount from quantity 2, exceeding product price' => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[3]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [3],
            ],
        ];
    }
}
