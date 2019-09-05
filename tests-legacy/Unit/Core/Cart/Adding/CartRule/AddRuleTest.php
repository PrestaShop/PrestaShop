<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace LegacyTests\Unit\Core\Cart\Adding\CartRule;

use Cart;
use CartRule;
use Configuration;
use LegacyTests\Unit\Core\Cart\AbstractCartTest;

/**
 * behat equivalent : Scenarii/Cart/Adding/CartRule/add_cartrule.feature
 */
class AddRuleTest extends AbstractCartTest
{
    protected $cartRulesFeatureActive;

    protected function setUp()
    {
        parent::setUp();
        $this->cartRulesFeatureActive = Configuration::get('PS_CART_RULE_FEATURE_ACTIVE');
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
    }

    protected function tearDown()
    {
        parent::tearDown();
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', $this->cartRulesFeatureActive);
    }

    /**
     * this test only check if cart rule can be applied on cart, depending on cart content and cart rule parameters
     *
     * @dataProvider cartRuleValidityProvider
     *
     * @param $productData
     * @param $cartRuleData
     * @param $shouldRulesBeApplied
     * @param $expectedProductCount
     * @param $expectedProductCountAfterRules
     */
    public function testCartRuleValidity(
        $productData,
        $cartRuleData,
        $shouldRulesBeApplied,
        $expectedProductCount,
        $expectedProductCountAfterRules
    ) {
        $this->addProductsToCart($productData);
        $this->assertEquals($expectedProductCount, Cart::getNbProducts($this->cart->id));
        $result = true;
        foreach ($cartRuleData as $cartRuleId) {
            $cartRule                = $this->getCartRuleFromFixtureId($cartRuleId);
            $result                  = $result && $cartRule->checkValidity(\Context::getContext(), false, false);
            $this->cartRulesInCart[] = $cartRule;
            $this->cart->addCartRule($cartRule->id);
        }
        $this->assertEquals($shouldRulesBeApplied, $result);
        $this->assertTrue(CartRule::haveCartRuleToday(0));
        $this->assertEquals($expectedProductCountAfterRules, Cart::getNbProducts($this->cart->id));
    }

    public function cartRuleValidityProvider()
    {
        return [
            'No product in cart should give a not valid cart rule insertion'                                               => [
                'products'                       => [],
                'cartRules'                      => [1],
                'shouldRulesBeApplied'           => false,
                'expectedProductCount'           => 0,
                'expectedProductCountAfterRules' => 0,
            ],
            '1 product in cart, cart rule is inserted correctly'                                                           => [
                'products'                       => [1 => 1],
                'cartRules'                      => [1],
                'shouldRulesBeApplied'           => true,
                'expectedProductCount'           => 1,
                'expectedProductCountAfterRules' => 1,
            ],
            '1 product in cart, cart rules are inserted correctly'                                                         => [
                'products'                       => [1 => 1],
                'cartRules'                      => [1, 2],
                'shouldRulesBeApplied'           => true,
                'expectedProductCount'           => 1,
                'expectedProductCountAfterRules' => 1,
            ],
            '1 product in cart, double cart rule not inserted'                                                             => [
                'products'                       => [1 => 1],
                'cartRules'                      => [1, 1],
                'shouldRulesBeApplied'           => false,
                'expectedProductCount'           => 1,
                'expectedProductCountAfterRules' => 1,
            ],
            '1 product in cart, cart rule giving gift, and global cart rule should be inserted without error'              => [
                'products'                       => [1 => 1],
                'cartRules'                      => [12, 1],
                'shouldRulesBeApplied'           => true,
                'expectedProductCount'           => 1,
                'expectedProductCountAfterRules' => 2,
            ],
            // test PR #8361
            '1 product in cart, cart rule giving gift out of stock, and global cart rule should be inserted without error' => [
                'products'                       => [1 => 1],
                'cartRules'                      => [13, 1],
                'shouldRulesBeApplied'           => true,
                'expectedProductCount'           => 1,
                'expectedProductCountAfterRules' => 1,
            ],
        ];
    }
}
