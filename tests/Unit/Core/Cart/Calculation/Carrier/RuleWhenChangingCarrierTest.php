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

namespace Tests\Unit\Core\Cart\Calculation\Carrier;

use Configuration;

/**
 * these tests aim to check the correct calculation of cart total when applying cart rules
 *
 * products are inserted as fixtures
 * products are inserted in cart from data providers
 * cart rules are inserted from data providers
 */
class RuleWhenChangingCarrierTest extends AbstractCarrierTest
{

    const CART_RULES_FIXTURES = [
        1 => ['code' => 'foo', 'priority' => 1, 'percent' => 55, 'amount' => 0, 'carrierRestrictionIds' => [2]],
        2 => ['code' => 'bar', 'priority' => 1, 'percent' => 55, 'amount' => 0, 'carrierRestrictionIds' => [1]],
        3 => ['code' => '', 'priority' => 1, 'percent' => 55, 'amount' => 0, 'carrierRestrictionIds' => [3]],
    ];

    const CARRIER_FIXTURES = [
        1 => [
            'name'   => 'carrier 1',
            'isFree' => false,
            'ranges' => [
                1 => [
                    'from'           => 0,
                    'to'             => 10000,
                    'shippingPrices' => [
                        1 => 3.1, // zoneId => price
                        2 => 4.3, // zoneId => price
                    ],
                ],
            ],
        ],
        2 => [
            'name'   => 'carrier 2',
            'isFree' => false,
            'ranges' => [
                1 => [
                    'from'           => 0,
                    'to'             => 10000,
                    'shippingPrices' => [
                        1 => 5.7, // zoneId => price
                        2 => 6.2, // zoneId => price
                    ],
                ],
            ],
        ],
        3 => [
            'name'   => 'carrier 3',
            'isFree' => false,
            'ranges' => [
                1 => [
                    'from'           => 0,
                    'to'             => 10000,
                    'shippingPrices' => [
                        1 => 5.7, // zoneId => price
                        2 => 6.2, // zoneId => price
                    ],
                ],
            ],
        ],
    ];

    public function testCarrierSpecificCartRuleCorresponding()
    {
        // specific setUp
        $this->setCartCarrierFromFixtureId(2);
        $this->setCartAddress(1);
        $this->addProductToCart(1, 1);

        // tests
        $cartRule = $this->getCartRuleFromFixtureId(2);
        $result   = $cartRule->checkValidity(\Context::getContext(), false, false);
        $this->assertFalse($result);

        $cartRule = $this->getCartRuleFromFixtureId(1);
        $result   = $cartRule->checkValidity(\Context::getContext(), false, false);
        $this->assertTrue($result);
        $this->cartRulesInCart[] = $cartRule;
        $result                  = $this->cart->addCartRule($cartRule->id);
        $this->assertTrue($result);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(1, $cartRules);

        $result = $cartRule->checkValidity(\Context::getContext(), false, false);
        $this->assertFalse($result);
        $this->cartRulesInCart[] = $cartRule;
        $result                  = $this->cart->addCartRule($cartRule->id);
        $this->assertFalse($result);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(1, $cartRules);
    }

    public function testCarrierSpecificCartRuleNotCorresponding()
    {
        // specific setUp
        $this->setCartCarrierFromFixtureId(2);
        $this->setCartAddress(1);
        $this->addProductToCart(1, 1);

        // tests
        $cartRule = $this->getCartRuleFromFixtureId(2);
        $result   = $cartRule->checkValidity(\Context::getContext(), false, false);
        $this->assertFalse($result);
    }

    public function testCarrierSpecificCartRuleRemovedWhenChangingCarrier()
    {
        // specific setUp
        $this->setCartCarrierFromFixtureId(2);
        $this->setCartAddress(1);
        $this->addProductToCart(1, 1);

        // tests
        $cartRule                = $this->getCartRuleFromFixtureId(1);
        $this->cartRulesInCart[] = $cartRule;
        $result                  = $this->cart->addCartRule($cartRule->id);
        $this->assertTrue($result);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(1, $cartRules);

        $this->setCartCarrierFromFixtureId(1);

        $cartRules = $this->cart->getCartRules();
        $this->assertCount(0, $cartRules);
    }

    public function testCarrierSpecificCartRuleWithoutCodeSetAndUnset()
    {
        // specific setUp
        $this->setCartAddress(1);
        $this->addProductToCart(1, 1);

        // tests
        $this->setCartCarrierFromFixtureId(2);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(0, $cartRules);
        $this->setCartCarrierFromFixtureId(3);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(1, $cartRules);
        $this->setCartCarrierFromFixtureId(2);
        $cartRules = $this->cart->getCartRules();
        $this->assertCount(0, $cartRules);
    }

}
