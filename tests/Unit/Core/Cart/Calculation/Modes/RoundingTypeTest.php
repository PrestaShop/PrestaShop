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

namespace Tests\Unit\Core\Cart\Calculation\Modes;

use Configuration;
use Order;
use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;
use Tools;

class RoundingTypeTest extends AbstractCartCalculationTest
{

    /**
     * Order::ROUND_ITEM
     * Order::ROUND_LINE
     * Order::ROUND_TOTAL
     */
    protected $defaultRoundingType;

    public function setUp()
    {
        // using Configuration instead of Adapter\Configuration because of different behavior
        $this->defaultRoundingType = Configuration::get('PS_ROUND_TYPE');

        parent::setUp();
    }

    public function tearDown()
    {
        // using Configuration instead of Adapter\Configuration because of different behavior
        Configuration::set('PS_ROUND_TYPE', $this->defaultRoundingType);

        parent::tearDown();
    }

    /**
     * sets the default rounding type
     *
     * @param string $roundingType Order::ROUND_ITEM|Order::ROUND_LINE|Order::ROUND_TOTAL
     */
    protected function setRoundingType($roundingType)
    {
        // using Configuration instead of Adapter\Configuration because of different behavior
        Configuration::set('PS_ROUND_TYPE', $roundingType);
    }

    /**
     * @dataProvider roundingTypeDataProvider
     */
    public function testRoundingTypes(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $roundingType
    ) {
        $this->resetCart();
        $this->setRoundingType($roundingType);

        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $totalV1 = $this->cart->getOrderTotalV1();
        $this->assertEquals($expectedTotal, $totalV1, 'V1 fail (tax incl)');
        $totalV2 = $this->cart->getOrderTotal();
        $this->assertEquals($expectedTotal, $totalV2, 'V2 fail (tax excl)');
    }

    public function roundingTypeDataProvider()
    {
        return [
            // ROUND_ITEM
            'ROUND_ITEM empty cart'                              => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingType'  => Order::ROUND_ITEM,
            ],
            'ROUND_ITEM one product in cart, quantity 1'         => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_ITEM,
            ],
            'ROUND_ITEM one product in cart, quantity 3'         => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2), 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_ITEM,
            ],
            'ROUND_ITEM 3 products in cart, several quantities'  => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(
                        3 * Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2)
                        + 2 * Tools::ps_round(static::PRODUCT_FIXTURES[2]['price'], 2)
                        + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2)
                        ,
                        2
                    )
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_ITEM,
            ],
            // ROUND_LINE
            'ROUND_LINE empty cart'                              => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingType'  => Order::ROUND_LINE,
            ],
            'ROUND_LINE one product in cart, quantity 1'         => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_LINE,
            ],
            'ROUND_LINE one product in cart, quantity 3'         => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_LINE,
            ],
            'ROUND_LINE 3 products in cart, several quantities'  => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(
                        Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2)
                        + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2)
                        + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2),
                        2
                    )
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_LINE,
            ],
            // ROUND_TOTAL
            'ROUND_TOTAL empty cart'                             => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingType'  => Order::ROUND_TOTAL,
            ],
            'ROUND_TOTAL one product in cart, quantity 1'        => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_TOTAL,
            ],
            'ROUND_TOTAL one product in cart, quantity 3'        => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_TOTAL,
            ],
            'ROUND_TOTAL 3 products in cart, several quantities' => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(
                        3 * static::PRODUCT_FIXTURES[1]['price']
                        + 2 * static::PRODUCT_FIXTURES[2]['price']
                        + static::PRODUCT_FIXTURES[3]['price'],
                        2
                    )
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => Order::ROUND_TOTAL,
            ],
        ];
    }
}
