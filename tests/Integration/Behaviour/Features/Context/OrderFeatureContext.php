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

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use LegacyTests\Unit\Core\Cart\CartToOrder\PaymentModuleFake;
use Order;
use OrderCartRule;

class OrderFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Order[]
     */
    protected $orders = [];

    /**
     * @When /^I validate my cart using payment module (fake)$/
     */
    public function validateCartWithPaymentModule($paymentModuleName)
    {
        switch ($paymentModuleName) {
            case 'fake':
                $paymentModule = new PaymentModuleFake();
                break;
            default:
                throw new \Exception(sprintf('Invalid payment module: %s' . $paymentModuleName));
        }

        // need to boot kernel for usage in $paymentModule->validateOrder()
        global $kernel;
        $previousKernel = $kernel;
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        // need to update secret_key in order to get payment working
        $cart = $this->getCurrentCart();
        $cart->secure_key = md5('xxx');
        $cart->update();
        $paymentModule->validateOrder(
            $cart->id,
            Configuration::get('PS_OS_CHEQUE'), // PS_OS_PAYMENT for payment-validated order
            0,
            'Unknown',
            null,
            [],
            null,
            false,
            $cart->secure_key
        );
        $order = Order::getByCartId($cart->id);
        $this->orders[] = $order;

        $kernel = $previousKernel;
    }

    /**
     * @Then /^current cart order total for products should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderProductTotal($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_products_wt : $order->total_products;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order total discount should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderTotalDiscount($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_discounts_tax_incl : $order->total_discounts_tax_excl;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order shipping fees should be (\d+\.\d+)( tax included| tax excluded)?$/
     */
    public function checkOrderShippingFees($expectedTotal, $taxes = null)
    {
        $order = $this->getCurrentCartOrder();
        $withTaxes = $taxes == ' tax excluded' ? false : true;
        $total = $withTaxes ? $order->total_shipping_tax_incl : $order->total_shipping_tax_excl;
        if ((float) $expectedTotal != (float) $total) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedTotal,
                    $total
                )
            );
        }
    }

    /**
     * @Then /^current cart order cart rules count should be (\d+)$/
     */
    public function checkOrderCartRulesCount($expectedCount)
    {
        $order = $this->getCurrentCartOrder();
        $count = count($order->getCartRules());
        if ($expectedCount != $count) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $expectedCount,
                    $count
                )
            );
        }
    }

    /**
     * @Then /^current cart order should have a discount in position (\d+) with an amount of (.+) tax included and (.+) tax excluded$/
     */
    public function checkOrderDiscount($position, $discountTaxIncluded, $discountTaxExcluded)
    {
        $order = $this->getCurrentCartOrder();
        $orderCartRulesData = $order->getCartRules();
        if (!isset($orderCartRulesData[$position - 1]['id_order_cart_rule'])) {
            throw new \Exception(
                sprintf('Undefined order cart rule on position #%s', $position)
            );
        }
        $orderCartRule = new OrderCartRule($orderCartRulesData[$position - 1]['id_order_cart_rule']);
        if ((float) $discountTaxIncluded != (float) $orderCartRule->value) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxIncluded,
                    $orderCartRule->value
                )
            );
        }
        if ((float) $discountTaxExcluded != (float) $orderCartRule->value_tax_excl) {
            throw new \RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxIncluded,
                    $orderCartRule->value_tax_excl
                )
            );
        }
    }

    protected function getCurrentCartOrder()
    {
        $cart = $this->getCurrentCart();
        if (null === $cart) {
            throw new \Exception('Current cart was not initialized');
        }
        $order = Order::getByCartId($cart->id);

        return $order;
    }

    /**
     * @AfterScenario
     */
    public function cleanOrderFixtures()
    {
        foreach ($this->orders as $order) {
            $order->delete();
        }
        $this->orders = [];
    }
}
