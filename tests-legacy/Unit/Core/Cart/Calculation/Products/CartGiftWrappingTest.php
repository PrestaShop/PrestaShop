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

namespace Tests\Unit\Core\Cart\Calculation\Products;

use Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;
use Configuration;

class CartGiftWrappingTest extends AbstractCartCalculationTest
{

    const GIFT_WRAPPING_PRICE = 5.3;
    protected $previousGiftWrapping;
    protected $previousGiftWrappingPrice;

    public function setUp()
    {
        parent::setUp();
        $this->previousGiftWrapping      = Configuration::get('PS_GIFT_WRAPPING');
        $this->previousGiftWrappingPrice = Configuration::get('PS_GIFT_WRAPPING_PRICE');
        Configuration::set('PS_GIFT_WRAPPING', true);
        Configuration::set('PS_GIFT_WRAPPING_PRICE', static::GIFT_WRAPPING_PRICE);
    }

    public function tearDown()
    {
        parent::tearDown();
        Configuration::set('PS_GIFT_WRAPPING', $this->previousGiftWrapping);
        Configuration::set('PS_GIFT_WRAPPING_PRICE', $this->previousGiftWrappingPrice);
    }

    /**
     * @dataProvider cartWithoutCartRulesProvider
     */
    public function testCartWithGiftWrapping($productData, $expectedTotal, $expectedTotalWithGiftWrapping)
    {
        $this->resetCart();
        $this->addProductsToCart($productData);
        $this->compareCartTotalTaxIncl($expectedTotal);
        $this->cart->gift = true;
        $this->compareCartTotalTaxIncl($expectedTotalWithGiftWrapping);
    }

    public function cartWithoutCartRulesProvider()
    {
        return [
            'empty cart'                             => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [],
            ],
            'one product in cart, quantity 1'        => [
                'products'                      => [1 => 1,],
                'expectedTotal'                 => static::PRODUCT_FIXTURES[1]['price']
                                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalWithGiftWrapping' => static::PRODUCT_FIXTURES[1]['price']
                                                   + static::DEFAULT_SHIPPING_FEE
                                                   + static::DEFAULT_WRAPPING_FEE
                                                   + static::GIFT_WRAPPING_PRICE,
            ],
            'one product in cart, quantity 3'        => [
                'products'                      => [1 => 3,],
                'expectedTotal'                 => 3 * static::PRODUCT_FIXTURES[1]['price']
                                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalWithGiftWrapping' => 3 * static::PRODUCT_FIXTURES[1]['price']
                                                   + static::DEFAULT_SHIPPING_FEE
                                                   + static::DEFAULT_WRAPPING_FEE
                                                   + static::GIFT_WRAPPING_PRICE,
            ],
            '3 products in cart, several quantities' => [
                'products'                      => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'                 => 3 * static::PRODUCT_FIXTURES[1]['price']
                                                   + 2 * static::PRODUCT_FIXTURES[2]['price']
                                                   + static::PRODUCT_FIXTURES[3]['price']
                                                   + static::DEFAULT_SHIPPING_FEE + static::DEFAULT_WRAPPING_FEE,
                'expectedTotalWithGiftWrapping' => 3 * static::PRODUCT_FIXTURES[1]['price']
                                                   + 2 * static::PRODUCT_FIXTURES[2]['price']
                                                   + static::PRODUCT_FIXTURES[3]['price']
                                                   + static::DEFAULT_SHIPPING_FEE
                                                   + static::DEFAULT_WRAPPING_FEE
                                                   + static::GIFT_WRAPPING_PRICE,
            ],
        ];
    }
}
