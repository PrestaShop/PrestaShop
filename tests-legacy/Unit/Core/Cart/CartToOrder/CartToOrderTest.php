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

namespace LegacyTests\Unit\Core\Cart\CartToOrder;

use Address;
use CartRule;
use Configuration;
use Context;
use Customer;
use LegacyTests\Unit\Core\Cart\Calculation\Taxes\CartTaxesTest;
use Mail;
use Order;
use OrderCartRule;

/**
 * this class test the correct copy from cart to order
 */
class CartToOrderTest extends CartTaxesTest
{

    protected $previousConfigurationMailMethod;

    public function setUp()
    {
        parent::setUp();

        global $kernel;
        $kernel = new \AppKernel('test', true);
        $kernel->boot();
        $this->previousConfigurationMailMethod = Configuration::get('PS_MAIL_METHOD');
        Configuration::set('PS_MAIL_METHOD', Mail::METHOD_DISABLE);
    }

    public function tearDown()
    {
        parent::tearDown();

        Configuration::set('PS_MAIL_METHOD', $this->previousConfigurationMailMethod);
    }

    /**
     * check the correct copy of data from cart to order when processing payment
     *
     * @dataProvider cartToOrderProvider
     *
     * @param $productData
     * @param $cartRuleData
     * @param $addressId
     * @param $expected_totalProduct_taxIncl
     * @param $expected_totalProduct_taxExcl
     * @param $expected_totalDiscount_taxIncl
     * @param $expected_totalDiscount_taxExcl
     * @param $expected_totalShipping_taxIncl
     * @param $expected_totalShipping_taxExcl
     * @param $expected_discounts_taxIncl
     * @param $expected_discounts_taxExcl
     * @param $expected_newVoucherValue
     */
    public function testCopyCartToOrder(
        $productData,
        $cartRuleData,
        $addressId,
        $expected_totalProduct_taxIncl,
        $expected_totalProduct_taxExcl,
        $expected_totalDiscount_taxIncl,
        $expected_totalDiscount_taxExcl,
        $expected_totalShipping_taxIncl,
        $expected_totalShipping_taxExcl,
        $expected_discounts_taxIncl,
        $expected_discounts_taxExcl,
        $expected_newVoucherValue = 0
    )
    {
        // prepare cart
        $this->resetCart();
        $this->cart->id_address_delivery = $addressId;
        $this->addProductsToCart($productData);
        $this->addCartRulesToCart($cartRuleData);

        // need to set customer to have a valid email
        $customer = new Customer();
        $customer->firstname = 'fake';
        $customer->lastname = 'fake';
        $customer->passwd = 'fakefake';
        $customer->email = 'fake@prestashop.com';
        $customer->add();
        $address = new Address($addressId);
        $address->id_customer = $customer->id;
        $address->update();

        Context::getContext()->updateCustomer($customer);

        // copy to order
        $paymentModule = new PaymentModuleFake;
        $paymentModule->validateOrder(
            $this->cart->id,
            Configuration::get('PS_OS_CHEQUE'), // PS_OS_PAYMENT for payment-validated order
            0,
            'Unknown',
            null,
            array(),
            null,
            false,
            $this->cart->secure_key
        );

        // tests
        $order = Order::getByCartId($this->cart->id);
        // compare global cart/order total
        $this->assertEquals($this->cart->getOrderTotal(false), $order->total_paid_tax_excl);
        $this->assertEquals($this->cart->getOrderTotal(true), $order->total_paid_tax_incl);

        // specific comparisons
        $this->assertEquals($expected_totalProduct_taxIncl, $order->total_products_wt);
        $this->assertEquals($expected_totalProduct_taxExcl, $order->total_products);
        $this->assertEquals($expected_totalDiscount_taxExcl, $order->total_discounts_tax_excl);
        $this->assertEquals($expected_totalDiscount_taxIncl, $order->total_discounts_tax_incl);
        $this->assertEquals($expected_totalShipping_taxExcl, $order->total_shipping_tax_excl);
        $this->assertEquals($expected_totalShipping_taxIncl, $order->total_shipping_tax_incl);

        $orderCartRulesData = $order->getCartRules();
        $this->assertCount(count($cartRuleData), $orderCartRulesData);
        // double check expected discounts and cart rules
        $discountCount = count($expected_discounts_taxIncl);
        $this->assertCount($discountCount, $cartRuleData);
        if ($discountCount != count($expected_discounts_taxExcl)) {
            throw new \Exception('discount with and without tax should have same count !');
        }

        // check unit discounts
        for ($i = 0; $i < $discountCount; $i++) {
            $orderCartRule = new OrderCartRule($orderCartRulesData[$i]['id_order_cart_rule']);
            $this->assertEquals($expected_discounts_taxIncl[$i], $orderCartRule->value);
            $this->assertEquals($expected_discounts_taxExcl[$i], $orderCartRule->value_tax_excl);
        }

        // check if new voucher should have been created
        if ($expected_newVoucherValue > 0) {
            $vouchers = CartRule::getCustomerCartRules($customer->id_lang, $customer->id, true, false);
            $this->assertCount(1, $vouchers);
            $voucher = new CartRule($vouchers[0]['id_cart_rule']);
            $this->assertEquals($expected_newVoucherValue, $voucher->reduction_amount);
        }
    }

    public function cartToOrderProvider()
    {
        return [
            '1 product in cart, 1 cart rule' => [
                'products' => [1 => 1],
                'cartRules' => [1],
                'addressId' => CartTaxesTest::ADDRESS_ID_1,
                'expected_totalProduct_taxIncl' => 20.6,
                'expected_totalProduct_taxExcl' => 19.81,
                'expected_totalDiscount_taxIncl' => 10.3,
                'expected_totalDiscount_taxExcl' => 9.91,
                'expected_totalShipping_taxIncl' => 7,
                'expected_totalShipping_taxExcl' => 7,
                'expected_discounts_taxIncl' => [10.3],
                'expected_discounts_taxExcl' => [9.91],
                'expected_newVoucherValue' => 0,
            ],
            '1 product in cart, 2 cart rules' => [
                'products' => [1 => 1],
                'cartRules' => [1, 2],
                'addressId' => CartTaxesTest::ADDRESS_ID_1,
                'expected_totalProduct_taxIncl' => 20.6,
                'expected_totalProduct_taxExcl' => 19.81,
                'expected_totalDiscount_taxIncl' => 15.45,
                'expected_totalDiscount_taxExcl' => 14.86,
                'expected_totalShipping_taxIncl' => 7,
                'expected_totalShipping_taxExcl' => 7,
                'expected_discounts_taxIncl' => [10.3, 5.15],
                'expected_discounts_taxExcl' => [9.91, 4.95],
                'expected_newVoucherValue' => 0,
            ],
            '3 product in cart, 1 cart rule' => [
                'products' => [1 => 1, 2 => 1, 3 => 2],
                'cartRules' => [1],
                'addressId' => CartTaxesTest::ADDRESS_ID_1,
                'expected_totalProduct_taxIncl' => 119.15,
                'expected_totalProduct_taxExcl' => 114.58,
                'expected_totalDiscount_taxIncl' => 59.58,
                'expected_totalDiscount_taxExcl' => 57.29,
                'expected_totalShipping_taxIncl' => 7,
                'expected_totalShipping_taxExcl' => 7,
                'expected_discounts_taxIncl' => [59.58],
                'expected_discounts_taxExcl' => [57.29],
                'expected_newVoucherValue' => 0,
            ],
            '3 product in cart, 2 cart rules' => [
                'products' => [1 => 1, 2 => 1, 3 => 2],
                'cartRules' => [1, 2],
                'addressId' => CartTaxesTest::ADDRESS_ID_1,
                'expected_totalProduct_taxIncl' => 119.15,
                'expected_totalProduct_taxExcl' => 114.58,
                'expected_totalDiscount_taxIncl' => 89.36,
                'expected_totalDiscount_taxExcl' => 85.94,
                'expected_totalShipping_taxIncl' => 7,
                'expected_totalShipping_taxExcl' => 7,
                'expected_discounts_taxIncl' => [59.58, 29.79],
                'expected_discounts_taxExcl' => [57.29, 28.65],
                'expected_newVoucherValue' => 0,
            ],
            '1 product in cart, 1 cart rule with too-much amount' => [
                'products' => [1 => 1],
                'cartRules' => [5],
                'addressId' => CartTaxesTest::ADDRESS_ID_1,
                'expected_totalProduct_taxIncl' => 20.6,
                'expected_totalProduct_taxExcl' => 19.81,
                'expected_totalDiscount_taxIncl' => 20.6,
                'expected_totalDiscount_taxExcl' => 19.81,
                'expected_totalShipping_taxIncl' => 7,
                'expected_totalShipping_taxExcl' => 7,
                'expected_discounts_taxIncl' => [20.6],
                'expected_discounts_taxExcl' => [19.81],
                'expected_newVoucherValue' => 480.19,
            ],
        ];
    }
}
