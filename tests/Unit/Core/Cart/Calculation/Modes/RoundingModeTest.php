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
use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;
use Tools;

class RoundingModeTest extends AbstractCartCalculationTest
{

    /**
     * PS_ROUND_UP
     * PS_ROUND_DOWN
     * PS_ROUND_HALF_UP
     * PS_ROUND_HALF_DOWN
     * PS_ROUND_HALF_DOWN
     * PS_ROUND_HALF_ODD
     */
    protected $defaultRoundingMode;

    public function setUp()
    {
        $this->defaultRoundingMode = Configuration::get('PS_PRICE_ROUND_MODE');

        parent::setUp();
    }

    public function tearDown()
    {
        Configuration::set('PS_PRICE_ROUND_MODE', $this->defaultRoundingMode);

        parent::tearDown();
    }

    /**
     * sets the default rounding mode
     *
     * @param string $roundingMode PS_ROUND_UP|PS_ROUND_DOWN|PS_ROUND_HALF_UP|PS_ROUND_HALF_DOWN|PS_ROUND_HALF_DOWN|PS_ROUND_HALF_ODD
     */
    protected function setRoundingMode($roundingMode)
    {
        Configuration::set('PS_PRICE_ROUND_MODE', $roundingMode);
    }

    /**
     * @dataProvider roundingModeDataProvider
     */
    public function testRoundingModes(
        $productData,
        $expectedTotal,
        $cartRuleData,
        $roundingMode
    ) {
        $this->resetCart();
        $this->setRoundingMode($roundingMode);

        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $totalV1 = $this->cart->getOrderTotal();
        $this->assertEquals($expectedTotal, $totalV1, 'V1 fail (tax incl)');
        $totalV2 = $this->cart->getOrderTotalV2();
        $this->assertEquals($expectedTotal, $totalV2, 'V2 fail (tax excl)');
    }

    public function roundingModeDataProvider()
    {
        return [
            // PS_ROUND_UP
            'PS_ROUND_UP empty cart'                                    => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_UP,
            ],
            'PS_ROUND_UP one product in cart, quantity 1'               => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_UP,
            ],
            'PS_ROUND_UP one product in cart, quantity 3'               => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_UP,
            ],
            'PS_ROUND_UP 3 products in cart, several quantities'        => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_UP)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_UP)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_UP,
            ],
            // PS_ROUND_DOWN
            'PS_ROUND_DOWN empty cart'                                  => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_DOWN,
            ],
            'PS_ROUND_DOWN one product in cart, quantity 1'             => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_DOWN,
            ],
            'PS_ROUND_DOWN one product in cart, quantity 3'             => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_DOWN,
            ],
            'PS_ROUND_DOWN 3 products in cart, several quantities'      => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_DOWN)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_DOWN)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_DOWN,
            ],
            // PS_ROUND_HALF_UP
            'PS_ROUND_HALF_UP empty cart'                               => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_UP,
            ],
            'PS_ROUND_HALF_UP one product in cart, quantity 1'          => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_UP,
            ],
            'PS_ROUND_HALF_UP one product in cart, quantity 3'          => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_UP,
            ],
            'PS_ROUND_HALF_UP 3 products in cart, several quantities'   => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_UP)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_HALF_UP)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_HALF_UP)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_UP,
            ],
            // PS_ROUND_HALF_DOWN
            'PS_ROUND_HALF_DOWN empty cart'                             => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_DOWN,
            ],
            'PS_ROUND_HALF_DOWN one product in cart, quantity 1'        => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_DOWN,
            ],
            'PS_ROUND_HALF_DOWN one product in cart, quantity 3'        => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_DOWN,
            ],
            'PS_ROUND_HALF_DOWN 3 products in cart, several quantities' => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_DOWN)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_HALF_DOWN)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_HALF_DOWN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_DOWN,
            ],
            // PS_ROUND_HALF_ODD
            'PS_ROUND_HALF_ODD empty cart'                              => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_ODD,
            ],
            'PS_ROUND_HALF_ODD one product in cart, quantity 1'         => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_ODD)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_ODD,
            ],
            'PS_ROUND_HALF_ODD one product in cart, quantity 3'         => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_ODD)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_ODD,
            ],
            'PS_ROUND_HALF_ODD 3 products in cart, several quantities'  => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_ODD)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_HALF_ODD)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_HALF_ODD)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_ODD,
            ],
            // PS_ROUND_HALF_EVEN
            'PS_ROUND_HALF_EVEN empty cart'                             => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_EVEN,
            ],
            'PS_ROUND_HALF_EVEN one product in cart, quantity 1'        => [
                'products'      => [1 => 1,],
                'expectedTotal' => Tools::ps_round(static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_EVEN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_EVEN,
            ],
            'PS_ROUND_HALF_EVEN one product in cart, quantity 3'        => [
                'products'      => [1 => 3,],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_EVEN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_EVEN,
            ],
            'PS_ROUND_HALF_EVEN 3 products in cart, several quantities' => [
                'products'      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal' => Tools::ps_round(3 * static::PRODUCT_FIXTURES[1]['price'], 2, PS_ROUND_HALF_EVEN)
                                   + Tools::ps_round(2 * static::PRODUCT_FIXTURES[2]['price'], 2, PS_ROUND_HALF_EVEN)
                                   + Tools::ps_round(static::PRODUCT_FIXTURES[3]['price'], 2, PS_ROUND_HALF_EVEN)
                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'cartRules'     => [],
                'roundingMode'  => PS_ROUND_HALF_EVEN,
            ],
        ];
    }
}
