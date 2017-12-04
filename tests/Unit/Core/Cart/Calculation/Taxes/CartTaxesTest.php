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

namespace Tests\Unit\Core\Cart\Calculation\Taxes;

use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

class CartProductTest extends AbstractCartCalculationTest
{

    const TAX_RULE_GROUPID_1 = 32;
    const ADDRESS_ID_1       = 2;
    const TAX_RATE_1         = 4;

    const ADDRESS_ID_2       = 4;
    const TAX_RULE_GROUPID_2 = 9;
    const TAX_RATE_2         = 6;

    const PRODUCT_FIXTURES = [
        1 => ['price' => 19.812, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_1],
        2 => ['price' => 32.388, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_1],
        3 => ['price' => 31.188, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_1],
        4 => ['price' => 35.567, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_1, 'outOfStock' => true],
        5 => ['price' => 19.812, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_2],
        6 => ['price' => 32.388, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_2],
        7 => ['price' => 31.188, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_2],
        8 => ['price' => 35.567, 'taxRuleGroupId' => self::TAX_RULE_GROUPID_2, 'outOfStock' => true],
    ];

    /**
     * @dataProvider cartTaxesProvider
     */
    public function testTaxes(
        $productData,
        $expectedTotalExcl,
        $expectedTotalTaxIncl,
        $cartRuleData,
        $addressId
    ) {
        $this->cart->id_address_delivery = $addressId;
        $this->resetCart();
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);

        // NO_PROD do not keep it in commit : round is for development only !
        $expectedTotalExcl    = floor($expectedTotalExcl * 10) / 10;
        $expectedTotalTaxIncl = floor($expectedTotalTaxIncl * 10) / 10;

        $totalV1TaxExcl = $this->cart->getOrderTotal(false);
        $totalV1TaxIncl = $this->cart->getOrderTotal(true);
        // NO_PROD do not keep it in commit : round is for development only !
        $totalV1TaxExcl = floor($totalV1TaxExcl * 10) / 10;
        $totalV1TaxIncl = floor($totalV1TaxIncl * 10) / 10;
        $this->assertEquals(\Tools::convertPrice($expectedTotalExcl), $totalV1TaxExcl, 'tax excl V1 fail');
        $this->assertEquals(\Tools::convertPrice($expectedTotalTaxIncl), $totalV1TaxIncl, 'tax incl V1 fail');

        $totalV2TaxExcl = $this->cart->getOrderTotalV2(false);
        $totalV2TaxIncl = $this->cart->getOrderTotalV2(true);
        // NO_PROD do not keep it in commit : round is for development only !
        $totalV2TaxExcl = floor($totalV2TaxExcl * 10) / 10;
        $totalV2TaxIncl = floor($totalV2TaxIncl * 10) / 10;
        $this->assertEquals(\Tools::convertPrice($expectedTotalExcl), $totalV2TaxExcl, 'tax excl V2 fail');
        $this->assertEquals(\Tools::convertPrice($expectedTotalTaxIncl), $totalV2TaxIncl, 'tax incl V2 fail');
    }

    public function cartTaxesProvider()
    {
        return [
            'empty cart'                                     => [
                'products'      => [],
                'expectedTotalTaxExcl' => 0,
                'expectedTotalTaxIncl' => 0,
                'cartRules'     => [],
                'addressId'     => static::ADDRESS_ID_1,
            ],
            'tax #1: one product in cart, quantity 1'        => [
                'products'             => [1 => 1,],
                'expectedTotalTaxExcl' => static::PRODUCT_FIXTURES[1]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => static::PRODUCT_FIXTURES[1]['price'] * (1 + static::TAX_RATE_1 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_1,
            ],
            'tax #2: one product in cart, quantity 1'        => [
                'products'             => [5 => 1,],
                'expectedTotalTaxExcl' => static::PRODUCT_FIXTURES[5]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => static::PRODUCT_FIXTURES[5]['price'] * (1 + static::TAX_RATE_2 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_2,
            ],
            'tax #1: one product in cart, quantity 3'        => [
                'products'             => [1 => 3,],
                'expectedTotalTaxExcl' => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => 3 * static::PRODUCT_FIXTURES[1]['price'] * (1 + static::TAX_RATE_1 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_1,
            ],
            'tax #2: one product in cart, quantity 3'        => [
                'products'             => [5 => 3,],
                'expectedTotalTaxExcl' => 3 * static::PRODUCT_FIXTURES[5]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => 3 * static::PRODUCT_FIXTURES[5]['price'] * (1 + static::TAX_RATE_2 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_2,
            ],
            'tax #1: 3 products in cart, several quantities' => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotalTaxExcl' => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + 2 * static::PRODUCT_FIXTURES[2]['price']
                                          + static::PRODUCT_FIXTURES[3]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => (3 * static::PRODUCT_FIXTURES[1]['price']
                                           + 2 * static::PRODUCT_FIXTURES[2]['price']
                                           + static::PRODUCT_FIXTURES[3]['price']) * (1 + static::TAX_RATE_1 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_1,
            ],
            'tax #2: 3 products in cart, several quantities' => [
                'products'             => [
                    6 => 2,
                    5 => 3,
                    7 => 1,
                ],
                'expectedTotalTaxExcl' => 3 * static::PRODUCT_FIXTURES[5]['price']
                                          + 2 * static::PRODUCT_FIXTURES[6]['price']
                                          + static::PRODUCT_FIXTURES[7]['price']
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalTaxIncl' => (3 * static::PRODUCT_FIXTURES[5]['price']
                                           + 2 * static::PRODUCT_FIXTURES[6]['price']
                                           + static::PRODUCT_FIXTURES[7]['price']) * (1 + static::TAX_RATE_2 / 100)
                                          + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'            => [],
                'addressId'            => static::ADDRESS_ID_2,
            ],
        ];
    }
}
