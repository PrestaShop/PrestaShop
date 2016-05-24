<?php

/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Integration\Classes;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use PHPUnit_Framework_Assert as Assert;
use PrestaShop\PrestaShop\Tests\Helper\DatabaseDump;
use Exception;
use Address;
use Carrier;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Db;
use Order;
use Product;
use Tools;
use Tax;
use TaxRulesGroup;
use TaxRule;

class CartGetOrderTotalTest extends IntegrationTestCase
{
    private static $dump;
    private static $id_address;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // Save the database to restore it later: we're not the only test running so let's leave things
        // the way we found them.
        self::$dump = DatabaseDump::create();

        // Some tests might have cleared the configuration
        Configuration::loadConfiguration();

        // Context needs a currency but doesn't set it by itself, use default one.
        Context::getContext()->currency = new Currency(self::getCurrencyId());

        // We'll base all our computations on the invoice address
        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');

        // We don't care about stock, abstract this away by allowing ordering out of stock products
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        // Create the address only once
        self::$id_address = self::makeAddress()->id;
    }

    public static function tearDownAfterClass()
    {
        // After the test, we restore the database in the state it was
        // before we started.
        self::$dump->restore();
    }

    /**
     * The private static methods below are used to setup the initial conditions
     * for our tests.
     * They should probably be refactored out of the test itself, but since they perform
     * tasks specifically designed for this test (and maybe misleading if used out of context),
     * I'm leaving them here for now.
     *
     * Methods starting with get should cache their result for performance,
     * methods starting with make should create a new object each time.
     */

    private static function deactivateCurrentCartRules()
    {
        Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'cart_rule SET active = 0');
    }

    private static function getLanguageId()
    {
        return (int)Context::getContext()->language->id;
    }

    private static function getDefaultLanguageId()
    {
        return Configuration::get('PS_LANG_DEFAULT');
    }

    private static function getCurrencyId()
    {
        return Configuration::get('PS_CURRENCY_DEFAULT');
    }

    private static function getCountryId()
    {
        return Configuration::get('PS_COUNTRY_DEFAULT');
    }

    private static function setRoundingMode($modeStr)
    {
        $mode = null;

        switch ($modeStr) {
            case 'up':
                $mode = PS_ROUND_UP;
                break;
            case 'down':
                $mode = PS_ROUND_DOWN;
                break;
            case 'half_up':
                $mode = PS_ROUND_HALF_UP;
                break;
            case 'half_down':
                $mode = PS_ROUND_HALF_DOWN;
                break;
            case 'half_even':
                $mode = PS_ROUND_HALF_DOWN;
                break;
            case 'hald_odd':
                $mode = PS_ROUND_HALF_ODD;
                break;
            default:
                throw new Exception(sprintf('Unknown rounding mode `%s`.', $modeStr));
        }

        Configuration::set('PS_PRICE_ROUND_MODE', $mode);

        return $mode;
    }

    private static function setRoundingType($typeStr)
    {
        $type = null;

        switch ($typeStr) {
            case 'item':
                $type = Order::ROUND_ITEM;
                break;
            case 'line':
                $type = Order::ROUND_LINE;
                break;
            case 'total':
                $type = Order::ROUND_TOTAL;
                break;
            default:
                throw new Exception(sprintf('Unknown rounding type `%s`.', $typeStr));
        }

        Configuration::set('PS_ROUND_TYPE', $type);

        return $type;
    }

    private static function setRoundingDecimals($nInt)
    {
        Configuration::set('PS_PRICE_DISPLAY_PRECISION', $nInt);

        return $nInt;
    }

    /**
     * $rate is e.g. 5.5, 20...
     * This is cached by $rate.
     */
    private static function getIdTax($rate)
    {
        static $taxes = array();

        $name = $rate.'% TAX';

        if (!array_key_exists($name, $taxes)) {
            $tax = new Tax(null, self::getDefaultLanguageId());
            $tax->name = $name;
            $tax->rate = $rate;
            $tax->active = true;
            Assert::assertTrue((bool)$tax->save()); // casting because actually returns 1, but not the point here.
            $taxes[$name] = $tax->id;
        }

        return $taxes[$name];
    }

    /**
     * This is cached by $rate.
     */
    private static function getIdTaxRulesGroup($rate)
    {
        static $groups = array();

        $name = $rate.'% TRG';

        if (!array_key_exists($name, $groups)) {
            $taxRulesGroup = new TaxRulesGroup(null, self::getDefaultLanguageId());
            $taxRulesGroup->name = $name;
            $taxRulesGroup->active = true;
            Assert::assertTrue((bool)$taxRulesGroup->save());

            $taxRule = new TaxRule(null, self::getDefaultLanguageId());
            $taxRule->id_tax = self::getIdTax($rate);
            $taxRule->id_country = self::getCountryId();
            $taxRule->id_tax_rules_group = $taxRulesGroup->id;

            Assert::assertTrue($taxRule->save());

            $groups[$name] = $taxRulesGroup->id;
        }

        return $groups[$name];
    }

    /**
     * This is cached by $name.
     */
    private static function makeProduct($name, $price, $id_tax_rules_group)
    {
        $product = new Product(null, false, self::getDefaultLanguageId());
        $product->id_tax_rules_group = $id_tax_rules_group;
        $product->name = $name;
        $product->price = $price;
        $product->link_rewrite = Tools::link_rewrite($name);
        Assert::assertTrue($product->save());
        return $product;
    }

    private static function makeAddress()
    {
        $address = new Address();
        $address->id_country = self::getCountryId();
        $address->firstname = 'Unit';
        $address->lastname = 'Tester';
        $address->address1 = '55 rue Raspail';
        $address->alias = microtime().getmypid();
        $address->city = 'Levallois';
        Assert::assertTrue($address->save());
        return $address;
    }

    private static function makeCart()
    {
        $cart = new Cart(null, self::getDefaultLanguageId());
        $cart->id_currency = self::getCurrencyId();
        $cart->id_address_invoice = self::$id_address;
        Assert::assertTrue($cart->save());
        Context::getContext()->cart = $cart;
        return $cart;
    }

    /**
     * null $shippingCost is interpreted as free shipping
     * Carriers are cached by $name.
     */
    private static function getIdCarrier($name, $shippingCost = null, $id_tax_rules_group = null)
    {
        static $carriers = array();

        if (!array_key_exists($name, $carriers)) {
            $carrier = new Carrier(null, self::getDefaultLanguageId());

            $carrier->name = $name;
            $carrier->delay = '28 days later';

            if (null === $shippingCost) {
                $carrier->is_free = true;
            } else {
                $carrier->range_behavior = false; // take highest range
                $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
            }

            $carrier->shipping_handling = false;

            Assert::assertTrue($carrier->save());

            if (null !== $id_tax_rules_group) {
                $carrier->setTaxRulesGroup($id_tax_rules_group);
            }

            if (null !== $shippingCost) {
                // Populate one range
                Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'range_price (id_carrier, delimiter1, delimiter2) VALUES (
                    '.(int)$carrier->id.',
                    0,1
                )');

                $id_range_price = Db::getInstance()->Insert_ID();
                Assert::assertGreaterThan(0, $id_range_price);

                // apply our shippingCost to all zones
                Db::getInstance()->execute(
                    'INSERT INTO '._DB_PREFIX_.'delivery (id_carrier, id_range_price, id_range_weight, id_zone, price)
                     SELECT '.(int)$carrier->id.', '.(int)$id_range_price.', 0, id_zone, '.(float)$shippingCost.'
                     FROM '._DB_PREFIX_.'zone'
                );

                // enable all zones
                Db::getInstance()->execute(
                    'INSERT INTO '._DB_PREFIX_.'carrier_zone (id_carrier, id_zone)
                     SELECT '.(int)$carrier->id.', id_zone FROM '._DB_PREFIX_.'zone'
                );
            }

            $carriers[$name] = $carrier->id;
        }

        return $carriers[$name];
    }

    private static function makeCartRule($amount, $type)
    {
        $cartRule = new CartRule(null, self::getDefaultLanguageId());

        $cartRule->name = $amount.' '.$type.' Cart Rule';

        $date_from = new \DateTime();
        $date_to = new \DateTime();

        $date_from->modify('-2 days');
        $date_to->modify('+2 days');

        $cartRule->date_from = $date_from->format('Y-m-d H:i:s');
        $cartRule->date_to = $date_to->format('Y-m-d H:i:s');

        $cartRule->quantity = 1;
        $cartRule->quantity_per_user;

        if ($type === 'before tax') {
            $cartRule->reduction_amount = $amount;
            $cartRule->reduction_tax = false;
        } elseif ($type === 'after tax') {
            $cartRule->reduction_amount = $amount;
            $cartRule->reduction_tax = true;
        } elseif ($type === '%') {
            $cartRule->reduction_percent = $amount;
        } else {
            throw new Exception(sprintf("Invalid CartRule type `%s`.", $type));
        }

        Assert::assertTrue($cartRule->save());

        return $cartRule;
    }

    /**
     * End of setup, real tests start here.
     */

    /**
     * Provide sensible defaults for tests that don't specify them.
     */
    public function setUp()
    {
        self::setRoundingType('line');
        self::setRoundingMode('half_up');
        self::setRoundingDecimals(2);
        // Pre-existing cart rules might mess up our test
        self::deactivateCurrentCartRules();
        // Something might have disabled CartRules :)
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
        Configuration::set('PS_ATCP_SHIPWRAP', false);
    }

    public function testBasicOnlyProducts()
    {
        $product = self::makeProduct('Hello Product', 10, self::getIdTaxRulesGroup(20));
        $cart = self::makeCart();

        $cart->updateQty(1, $product->id);

        $this->assertEquals(10, $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
        $this->assertEquals(12, $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS));
    }

    public function testCartBothWithFreeCarrier()
    {
        $product = self::makeProduct('Hello Product', 10, self::getIdTaxRulesGroup(20));
        $cart = self::makeCart();

        $id_carrier = self::getIdCarrier('free');

        $cart->updateQty(1, $product->id);
        $this->assertEquals(10, $cart->getOrderTotal(false, Cart::BOTH, null, $id_carrier));
        $this->assertEquals(12, $cart->getOrderTotal(true, Cart::BOTH, null, $id_carrier));
    }

    public function testCartBothWithPaidCarrier()
    {
        $product = self::makeProduct('Hello Product', 10, self::getIdTaxRulesGroup(10));
        $cart = self::makeCart();

        $id_carrier = self::getIdCarrier('costs 2', 2, self::getIdTaxRulesGroup(10));

        $cart->updateQty(1, $product->id);
        $this->assertEquals(12, $cart->getOrderTotal(false, Cart::BOTH, null, $id_carrier));
        $this->assertEquals(13.2, $cart->getOrderTotal(true, Cart::BOTH, null, $id_carrier));
    }

    public function testBasicRoundTypeLine()
    {
        self::setRoundingType('line');

        $product_a = self::makeProduct('A Product', 1.236, self::getIdTaxRulesGroup(20));
        $product_b = self::makeProduct('B Product', 2.345, self::getIdTaxRulesGroup(20));

        $cart = self::makeCart();

        $cart->updateQty(1, $product_a->id);
        $cart->updateQty(1, $product_b->id);

        $this->assertEquals(3.59, $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
        $this->assertEquals(4.29, $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS));
    }

    public function testBasicRoundTypeTotal()
    {
        self::setRoundingType('total');

        $product_a = self::makeProduct('A Product', 1.236, self::getIdTaxRulesGroup(20));
        $product_b = self::makeProduct('B Product', 2.345, self::getIdTaxRulesGroup(20));

        $cart = self::makeCart();

        $cart->updateQty(1, $product_a->id);
        $cart->updateQty(1, $product_b->id);

        $this->assertEquals(3.58, $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));
        $this->assertEquals(4.30, $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS));
    }

    public function testBasicCartRuleAmountBeforeTax()
    {
        $id_carrier = self::getIdCarrier('free');

        $product = self::makeProduct('Yo Product', 10, self::getIdTaxRulesGroup(20));


        self::makeCartRule(5, 'before tax')->id;
        $cart = self::makeCart();

        $cart->updateQty(1, $product->id);

        // Control the result without the CartRule
        $this->assertEquals(10, $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS));

        // Check that the CartRule is applied
        $this->assertEquals(5, $cart->getOrderTotal(false, Cart::BOTH, null, $id_carrier));
        $this->assertEquals(6, $cart->getOrderTotal(true, Cart::BOTH, null, $id_carrier));
    }

    /**
     * This test checks that if PS_ATCP_SHIPWRAP is set to true then:
     * - the shipping cost of the carrier is understood as tax included instead of tax excluded
     * - the tax excluded shipping cost is deduced from the tax included shipping cost
     * 	 by removing the average tax rate of the cart
     */
    public function testAverageTaxOfCartProducts_ShippingTax()
    {
        Configuration::set('PS_ATCP_SHIPWRAP', true);

        $highProduct = self::makeProduct('High Product', 10, self::getIdTaxRulesGroup(20));
        $lowProduct = self::makeProduct('Low Product', 10, self::getIdTaxRulesGroup(10));
        $cart = self::makeCart();

        $id_carrier = self::getIdCarrier('costs 5 with tax', 5, null);

        $cart->updateQty(1, $highProduct->id);
        $cart->updateQty(3, $lowProduct->id);

        $preTax = round(5 / (1 + (3 * 10 + 1 * 20) / (4 * 100)), 2);

        $this->assertEquals($preTax, $cart->getOrderTotal(false, Cart::ONLY_SHIPPING, null, $id_carrier));
        $this->assertEquals(5, $cart->getOrderTotal(true, Cart::ONLY_SHIPPING, null, $id_carrier));
    }
}
