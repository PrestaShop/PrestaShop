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

namespace LegacyTests\Unit\Core\Cart\Calculation\SpecificPriceRules;

/**
 * behat equivalent : Scenarii/Cart/Calculation/SpecificPriceRule/price_set.feature
 */
class SpecificPriceRulePriceSetTest extends AbstractSpecificPriceRuleTest
{
    const SPECIFIC_PRICE_RULES_FIXTURES = [
        1 => ['reductionType' => 'percentage', 'reduction' => 0, 'fromQuantity' => 1, 'price' => 2],
        2 => ['reductionType' => 'percentage', 'reduction' => 0, 'fromQuantity' => 2, 'price' => 2],
    ];

    /**
     * @dataProvider specificPriceRulePriceSetProvider
     *
     * @param $productData
     * @param $expectedTotal
     * @param $cartRuleData
     * @param $specificCartRuleData
     *
     * @throws \Exception
     */
    public function testSpecificPricePriceSetRule(
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

    public function specificPriceRulePriceSetProvider()
    {
        return [
            '1 product in cart, quantity 1, one rule price set from quantity 1'                                  => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '1 product in cart, quantity 1, one rule price set from quantity 2'                                  => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
            '1 product in cart, quantity 3, one rule price set from quantity 1'                                  => [
                'products'             => [
                    1 => 3,
                ],
                'expectedTotal'        => 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '1 product in cart, quantity 3, one rule price set from quantity 2'                                  => [
                'products'             => [
                    1 => 3,
                ],
                'expectedTotal'        => 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[2]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
            '3 products in cart, several quantities, one rule price set from quantity 1'                         => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + 2 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1],
            ],
            '3 products in cart, several quantities, one rule price set from quantity 2'                         => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + 2 * static::SPECIFIC_PRICE_RULES_FIXTURES[1]['price']
                + static::PRODUCT_FIXTURES[3]['price']
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2],
            ],
        ];
    }
}
