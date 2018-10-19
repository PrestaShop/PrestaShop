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

namespace Tests\Unit\Core\Cart\Calculation\Carrier;

use Configuration;

class CarrierTest extends AbstractCarrierTest
{

    /**
     * @dataProvider shippingFeesProviderCarrier1
     */
    public function testShippingFeesCarrier1(
        $productData,
        $expectedTotal,
        $expectedShippingFees,
        $expectedWrappingFees,
        $cartRuleData,
        $addressId,
        $carrierId
    ) {
        // specific setUp
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->setCartCarrierFromFixtureId($carrierId);
        $this->setCartAddress($addressId);

        // assertions
        $this->assertEquals($expectedShippingFees, $this->cart->getPackageShippingCost($this->cart->id_carrier));
        $this->compareCartTotalTaxIncl($expectedTotal);
    }

    /**
     * @dataProvider shippingFeesProviderCarrier2
     */
    public function testShippingFeesCarrier2(
        $productData,
        $expectedTotal,
        $expectedShippingFees,
        $expectedWrappingFees,
        $cartRuleData,
        $addressId,
        $carrierId
    ) {
        // specific setUp
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);
        $this->setCartCarrierFromFixtureId($carrierId);
        $this->setCartAddress($addressId);

        // assertions
        $this->assertEquals(
            round($expectedShippingFees, 1),
            round($this->cart->getPackageShippingCost($this->cart->id_carrier), 1)
        );
        $this->compareCartTotalTaxIncl($expectedTotal);
    }

    public function shippingFeesProviderCarrier1()
    {
        $shippingHandling = (float) Configuration::get('PS_SHIPPING_HANDLING');

        return [
            'empty cart'                             => [
                'products'             => [],
                'expectedTotal'        => 0,
                'expectedShippingFees' => 0,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 1,
            ],
            'one product in cart, quantity 1'        => [
                'products'             => [1 => 1,],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                                          + static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 1,
            ],
            'one product in cart, quantity 3'        => [
                'products'             => [1 => 3,],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 1,
            ],
            '3 products in cart, several quantities' => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + 2 * static::PRODUCT_FIXTURES[2]['price']
                                          + static::PRODUCT_FIXTURES[3]['price']
                                          + static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[1]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 1,
            ],
        ];
    }

    public function shippingFeesProviderCarrier2()
    {
        $shippingHandling = (float) Configuration::get('PS_SHIPPING_HANDLING');

        return [
            'empty cart'                             => [
                'products'             => [],
                'expectedTotal'        => 0,
                'expectedShippingFees' => 0,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
            'one product in cart, quantity 1'        => [
                'products'             => [1 => 1,],
                'expectedTotal'        => static::PRODUCT_FIXTURES[1]['price']
                                          + static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
            'one product in cart, quantity 3'        => [
                'products'             => [1 => 3,],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
            '3 products in cart, several quantities' => [
                'products'             => [
                    2 => 2,
                    1 => 3,
                    3 => 1,
                ],
                'expectedTotal'        => 3 * static::PRODUCT_FIXTURES[1]['price']
                                          + 2 * static::PRODUCT_FIXTURES[2]['price']
                                          + static::PRODUCT_FIXTURES[3]['price']
                                          + static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
        ];
    }
}
