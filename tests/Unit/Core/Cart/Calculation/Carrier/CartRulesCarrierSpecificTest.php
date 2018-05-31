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

/**
 * these tests aim to check the correct calculation of cart total when applying cart rules
 *
 * products are inserted as fixtures
 * products are inserted in cart from data providers
 * cart rules are inserted from data providers
 */
class CartRulesCarrierSpecificTest extends AbstractCarrierTest
{

    const CART_RULES_FIXTURES = [
        1  => array('code'=>'', 'priority' => 1, 'percent' => 55, 'amount' => 0, 'carrierRestrictionIds'=>[2]),
    ];

    /**
     * @dataProvider cartWithOneProductSpecificCartRulesAmountProvider
     */
    public function testCartWithOneCarrierSpecificCartRule(
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
        $this->setCartCarrier($carrierId);
        $this->setCartAddress($addressId);

        // assertions
        $this->compareCartTotalTaxIncl($expectedTotal);
    }

    public function cartWithOneProductSpecificCartRulesAmountProvider()
    {
        $shippingHandling = (float) Configuration::get('PS_SHIPPING_HANDLING');

        return [
            ' carrier #1: empty cart'                             => [
                'products'             => [],
                'expectedTotal'        => 0,
                'expectedShippingFees' => 0,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 1,
            ],
            ' carrier #1: one product in cart, quantity 1'        => [
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
            ' carrier #2 (voucher specific): empty cart'                             => [
                'products'             => [],
                'expectedTotal'        => 0,
                'expectedShippingFees' => 0,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
            /*
            // following is testing te bug http://forge.prestashop.com/browse/BOOM-3307
            ' carrier #2 (voucher specific): one product in cart, quantity 1'        => [
                'products'             => [1 => 1,],
                'expectedTotal'        => (1 - static::CART_RULES_FIXTURES[1]['percent'] / 100)
                                          * static::PRODUCT_FIXTURES[1]['price']
                                          + static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling + static::DEFAULT_WRAPPING_FEE,
                'expectedShippingFees' => static::CARRIER_FIXTURES[2]['ranges'][1]['shippingPrices'][static::COUNTRY_FIXTURES[static::ADDRESS_FIXTURES[1]['countryIsoCode']]['zoneId']]
                                          + $shippingHandling,
                'expectedWrappingFees' => 0,
                'cartRules'            => [],
                'addressId'            => 1,
                'carrierId'            => 2,
            ],
            */
        ];
    }
}
