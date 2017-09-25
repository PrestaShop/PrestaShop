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

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Cart\Calculation\CartRules;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use PrestaShop\PrestaShop\Tests\Unit\ContextMocker;
use PrestaShop\PrestaShop\Tests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;

/**
 * these tests aim to check the correct calculation of cart total when applying cart rules
 *
 * products are inserted as fixtures
 * products are inserted in cart from data providers
 * cart rules are inserted from data providers
 */
class CartRulesTest extends AbstractCartCalculationTest
{

    /**
     * @dataProvider cartWithOneCartRulePercentProvider
     */
    public function testCartWithOneCartRulePercent($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithOneCartRuleAmountProvider
     */
    public function testCartWithOneCartRuleAmount($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleCartRulesPercentProvider
     */
    public function testCartWithMultipleCartRulesPercent($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleCartRulesAmountProvider
     */
    public function testCartWithMultipleCartRulesAmount($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleCartRulesMixedProvider
     */
    public function testCartWithMultipleCartRulesMixed($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithOneProductSpecificCartRulesAmountProvider
     */
    public function testCartWithOneProductSpecificCartRulesAmount($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithOneProductSpecificCartRulesPercentProvider
     */
    public function testCartWithOneProductSpecificCartRulesPercent($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleProductSpecificCartRulesPercentProvider
     */
    public function testCartWithMultipleProductSpecificCartRulesPercent($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleProductSpecificCartRulesMixedProvider
     */
    public function testCartWithMultipleProductSpecificCartRulesMixed($productDatas, $expectedTotal, $cartRuleDatas)
    {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithMultipleProductOutOfStockSpecificCartRulesMixedProvider
     */
    public function testCartWithMultipleProductOutOfStockSpecificCartRulesMixed(
        $productDatas,
        $expectedTotal,
        $cartRuleDatas
    ) {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
    }

    /**
     * @dataProvider cartWithGiftProvider
     */
    public function testCartWithGift(
        $productDatas,
        $expectedTotal,
        $cartRuleDatas,
        $expectedProductCount
    ) {
        $this->addProductsToCart($productDatas);
        $this->addCartRulesToCart($cartRuleDatas);
        $this->compareCartTotal($expectedTotal);
        $this->assertEquals($expectedProductCount, \Cart::getNbProducts($this->cart->id));
    }

    public function cartWithOneCartRulePercentProvider()
    {
        return [
            'empty cart'                                                     => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [2],
            ],
            'one product in cart, quantity 1, one 50% global voucher'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 9.9,
                'cartRules'     => [2],
            ],
            'one product in cart, quantity 3, one 50% global voucher'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 29.72,
                'cartRules'     => [2],
            ],
            '3 products in cart, several quantities, one 50% global voucher' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 77.7,
                'cartRules'     => [2],
            ],
        ];
    }

    public function cartWithOneCartRuleAmountProvider()
    {
        return [
            'empty cart'                                                                                      => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [4],
            ],
            'one product in cart, quantity 1, one 5€ global voucher'                                          => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 13.81,
                'cartRules'     => [4],
            ],
            'one product in cart, quantity 1, one 500€ global voucher'                                        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 0, // voucher exceeds total
                'cartRules'     => [5],
            ],
            'one product in cart, quantity 3, one 5€ global voucher'                                          => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 53.44,
                'cartRules'     => [4],
            ],
            '3 products in cart, several quantities, one 5€ global voucher (reduced product at first place)'  => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 149.41,
                'cartRules'     => [4],
            ],
            '3 products in cart, several quantities, one 5€ global voucher (reduced product at second place)' => [
                'products'      => [
                    1 => 3, // 59.43
                    2 => 2, // 64.776
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 149.41,
                'cartRules'     => [4],
            ],
            '3 products in cart, several quantities, one 500€ global voucher'                                 => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 0, // voucher exceeds total
                'cartRules'     => [5],
            ],
        ];
    }

    public function cartWithMultipleCartRulesPercentProvider()
    {
        return [
            'empty cart'                                                  => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [2, 3],
            ],
            'one product in cart, quantity 1, 2 % global vouchers'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 8.91,
                'cartRules'     => [2, 3],
            ],
            'one product in cart, quantity 3, 2 % global vouchers'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 26.75,
                'cartRules'     => [2, 3],
            ],
            '3 products in cart, several quantities, 2 % global vouchers' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 69.93,
                'cartRules'     => [2, 3],
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
            ],
            'one product in cart, quantity 1, one 5€ global voucher, one 10€ global voucher'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 1.81,
                'cartRules'     => [4, 6],
            ],
            'one product in cart, quantity 3, one 5€ global voucher, one 10€ global voucher'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 41.44,
                'cartRules'     => [4, 6],
            ],
            '3 products in cart, several quantities, one 5€ global voucher, one 10€ global voucher' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 137.41,
                'cartRules'     => [4, 6],
            ],
        ];
    }

    public function cartWithMultipleCartRulesMixedProvider()
    {
        return [
            'one product in cart, quantity 1, one 50% global voucher, one 5€ global voucher'   => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 3.9,
                'cartRules'     => [2, 4],
            ],
            'one product in cart, quantity 1, one 50% global voucher, one 500€ global voucher' => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 0,
                'cartRules'     => [2, 5],
            ],
            'one product in cart, quantity 3, one 5€ global voucher, one 50% global voucher'   => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 6.9,
                'cartRules'     => [4, 7],
            ],
            'one product in cart, quantity 3, one 500€ global voucher, one 50% global voucher' => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 0,
                'cartRules'     => [5, 7],
            ],
        ];
    }

    public function cartWithOneProductSpecificCartRulesAmountProvider()
    {
        return [
            'empty cart'                                                                      => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [8],
            ],
            'one product in cart, quantity 1, one specific 5€ voucher on product #2'          => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 19.81, // specific discount not applied on product #1
                'cartRules'     => [8],
            ],
            'one product in cart, quantity 3, one specific 5€ voucher on product #2'          => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 59.44, // specific discount not applied on product #1
                'cartRules'     => [8],
            ],
            '3 products in cart, several quantities, one specific 5€ voucher on product #2'   => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 149.41,
                'cartRules'     => [8],
            ],
            '3 products in cart, several quantities, one specific 500€ voucher on product #2' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 85.96, // voucher exceeds #2 total
                'cartRules'     => [9],
            ],
        ];
    }

    public function cartWithOneProductSpecificCartRulesPercentProvider()
    {
        return [
            'empty cart'                                                                     => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [10],
            ],
            'one product in cart, quantity 1, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 19.81, // specific discount not applied on product #1
                'cartRules'     => [10],
            ],
            'one product in cart, quantity 3, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 59.44, // specific discount not applied on product #1
                'cartRules'     => [10],
            ],
            'one product #2 in cart, quantity 3, one specific 50% voucher on product #2'     => [
                'products'      => [
                    2 => 3,
                ],
                'expectedTotal' => 48.58,
                'cartRules'     => [10],
            ],
            '3 products in cart, several quantities, one specific 50% voucher on product #2' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 123.02,
                'cartRules'     => [10],
            ],
        ];
    }

    public function cartWithMultipleProductSpecificCartRulesPercentProvider()
    {
        return [
            'empty cart'                                                                     => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [10, 11],
            ],
            'one product in cart, quantity 1, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 19.81, // specific discount not applied on product #1
                'cartRules'     => [10, 11],
            ],
            'one product in cart, quantity 3, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 59.44, // specific discount not applied on product #1
                'cartRules'     => [10, 11],
            ],
            'one product #2 in cart, quantity 3, one specific 50% voucher on product #2'     => [
                'products'      => [
                    2 => 3,
                ],
                'expectedTotal' => 43.72,
                'cartRules'     => [10, 11],
            ],
            '3 products in cart, several quantities, one specific 50% voucher on product #2' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 119.78,
                'cartRules'     => [10, 11],
            ],
        ];
    }

    public function cartWithMultipleProductSpecificCartRulesMixedProvider()
    {
        return [
            'empty cart'                                                                     => [
                'products'      => [],
                'expectedTotal' => 0,
                'cartRules'     => [8, 10],
            ],
            'one product in cart, quantity 1, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 1,
                ],
                'expectedTotal' => 19.81, // specific discount not applied on product #1
                'cartRules'     => [8, 10],
            ],
            'one product in cart, quantity 3, one specific 50% voucher on product #2'        => [
                'products'      => [
                    1 => 3,
                ],
                'expectedTotal' => 59.44, // specific discount not applied on product #1
                'cartRules'     => [8, 10],
            ],
            'one product #2 in cart, quantity 3, one specific 50% voucher on product #2'     => [
                'products'      => [
                    2 => 3,
                ],
                'expectedTotal' => 46.08,
                'cartRules'     => [8, 10],
            ],
            '3 products in cart, several quantities, one specific 50% voucher on product #2' => [
                'products'      => [
                    2 => 2, // 64.776
                    1 => 3, // 59.43
                    3 => 1, // 31.188
                    // total without rule : 155.41
                ],
                'expectedTotal' => 120.52,
                'cartRules'     => [8, 10],
            ],
        ];
    }

    public function cartWithMultipleProductOutOfStockSpecificCartRulesMixedProvider()
    {
        return [

            'one product in cart, quantity 1, out of stock' => [
                'products'      => [
                    4 => 1,
                ],
                'expectedTotal' => 35.57,
                'cartRules'     => [],
            ],
            '2 products in cart, one is out of stock'       => [
                'products'      => [
                    1 => 3,
                    4 => 1,
                ],
                'expectedTotal' => 95.01,
                'cartRules'     => [],
            ],
        ];
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
                'expectedProductCount' => 2,
            ],
            '2 products in cart, one cart rule offering a gift (out of stock) and a global 10% discount'                                => [
                'products'             => [
                    1 => 3,
                    4 => 1,
                ],
                'expectedTotal'        => 53.496,
                'cartRules'            => [13],
                'expectedProductCount' => 5,
            ],
            '2 products in cart, one cart rule offering a gift (in stock) and a global 10% discount'                                    => [
                'products'             => [
                    3 => 3,
                    4 => 1,
                ],
                'expectedTotal'        => 56.138,
                'cartRules'            => [12],
                'expectedProductCount' => 5,
            ],
        ];
    }
}
