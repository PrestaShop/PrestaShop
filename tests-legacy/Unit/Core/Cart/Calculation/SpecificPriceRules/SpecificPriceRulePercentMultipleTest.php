<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Cart\Calculation\SpecificPriceRules;

class SpecificPriceRulePercentMultipleTest extends AbstractSpecificPriceRuleTest
{
    const SPECIFIC_PRICE_RULES_FIXTURES = [
        1 => ['reductionType' => 'percentage', 'reduction' => 23, 'fromQuantity' => 1],
        2 => ['reductionType' => 'percentage', 'reduction' => 15, 'fromQuantity' => 1],
    ];

    /**
     * @dataProvider specificPriceRulePercentMultipleProvider
     *
     * @param $productData
     * @param $expectedTotal
     * @param $cartRuleData
     * @param $specificCartRuleData
     *
     * @throws \Exception
     */
    public function testSpecificPriceRulePercentMultiple(
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

    public function specificPriceRulePercentMultipleProvider()
    {
        return [
            '1 product in cart, quantity 1, 2 rule percent from quantity 1, first is used'           => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                * (1 - static::SPECIFIC_PRICE_RULES_FIXTURES[1]['reduction'] / 100)
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [1, 2],
            ],
            '1 product in cart, quantity 1, 2 rule percent from quantity 1, reversed, first is used' => [
                'products'             => [
                    1 => 1,
                ],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                * (1 - static::SPECIFIC_PRICE_RULES_FIXTURES[2]['reduction'] / 100)
                + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'specificCartRuleData' => [2, 1],
            ],
        ];
    }
}
