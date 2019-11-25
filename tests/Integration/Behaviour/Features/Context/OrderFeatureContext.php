<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

use AppKernel;
use Configuration;
use Exception;
use LegacyTests\Unit\Core\Cart\CartToOrder\PaymentModuleFake;
use Order;
use OrderCartRule;
use PrestaShopExceptionCore;
use Product;
use RuntimeException;
use Shop;

class OrderFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Order[]
     */
    protected $orders = [];


    /**
     * @BeforeScenario
     * @throws PrestaShopExceptionCore
     */
    public function before()
    {
        $defaultShopId = Configuration::get('PS_SHOP_DEFAULT');
        Shop::setContext(Shop::CONTEXT_SHOP, $defaultShopId);
        // needed because if no controller defined then CONTEXT_ALL is selected and exception is thrown
        \Context::getContext()->controller = 'test';
    }

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
                throw new Exception(sprintf('Invalid payment module: %s' . $paymentModuleName));
        }

        // need to boot kernel for usage in $paymentModule->validateOrder()
        global $kernel;
        $previousKernel = $kernel;
        $kernel = new AppKernel('test', true);
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
            throw new RuntimeException(
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
            throw new RuntimeException(
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
            throw new RuntimeException(
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
            throw new RuntimeException(
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
            throw new Exception(
                sprintf('Undefined order cart rule on position #%s', $position)
            );
        }
        $orderCartRule = new OrderCartRule($orderCartRulesData[$position - 1]['id_order_cart_rule']);
        if ((float) $discountTaxIncluded != (float) $orderCartRule->value) {
            throw new RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxIncluded,
                    $orderCartRule->value
                )
            );
        }
        if ((float) $discountTaxExcluded != (float) $orderCartRule->value_tax_excl) {
            throw new RuntimeException(
                sprintf(
                    'Expects %s, got %s instead',
                    $discountTaxExcluded,
                    $orderCartRule->value_tax_excl
                )
            );
        }
    }

    /**
     * @Then order :reference should have :quantity products in total
     */
    public function assertOrderProductsQuantity($reference, $quantity)
    {
        /** @var Order $order */
        $order = SharedStorage::getStorage()->get($reference);
        $orderProducts = $order->getProductsDetail();

        $totalQuantity = 0;

        foreach ($orderProducts as $orderProduct) {
            $totalQuantity += (int) $orderProduct['product_quantity'];
        }

        if ($totalQuantity !== (int) $quantity) {
            throw new Exception(sprintf(
                'Order should have "%d" products, but has "%d".',
                $totalQuantity,
                $quantity
            ));
        }
    }

    /**
     * @Given there is order with reference :orderReference
     */
    public function thereIsOrderWithReference($orderReference)
    {
        $orders = Order::getByReference($orderReference);

        if (0 === $orders->count()) {
            throw new Exception(sprintf('Order with reference "%s" does not exist.', $orderReference));
        }
    }

    /**
     * @Then order :reference should have free shipping
     */
    public function createdOrderShouldHaveFreeShipping($reference)
    {
        $order = SharedStorage::getStorage()->get($reference);

        foreach ($order->getCartRules() as $cartRule) {
            if ($cartRule['free_shipping']) {
                return;
            }
        }

        throw new Exception('Order should have free shipping.');
    }

    /**
     * @Then order :reference should have :paymentModuleName payment method
     */
    public function createdOrderShouldHavePaymentMethod($reference, $paymentModuleName)
    {
        $order = SharedStorage::getStorage()->get($reference);

        if ($order->module !== $paymentModuleName) {
            throw new Exception(sprintf(
                'Order should have "%s" payment method, but has "%s" instead.',
                $paymentModuleName,
                $order->payment
            ));
        }
    }

    /**
     * @Given order with reference :orderReference does not contain product with reference :productReference
     */
    public function orderDoesNotContainProductWithReference($orderReference, $productReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $productId = Product::getIdByReference($productReference);

        if ($order->orderContainProduct($productId)) {
            throw new RuntimeException(
                sprintf(
                    'Order with reference "%s" contains product with reference "%s".',
                    $orderReference,
                    $productReference
                )
            );
        }
    }

    /**
     * @Then order :orderReference should contain :quantity products with reference :productReference
     */
    public function orderContainsProductWithReference($orderReference, $quantity, $productReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $productId = (int) Product::getIdByReference($productReference);

        if (!$order->orderContainProduct($productId)) {
            throw new RuntimeException(
                sprintf(
                    'Order with reference "%s" does not contain product with reference "%s".',
                    $orderReference,
                    $productReference
                )
            );
        }

        $orderDetails = $order->getOrderDetailList();

        foreach ($orderDetails as $orderDetail) {
            if ((int) $orderDetail['product_id'] === $productId &&
                (int) $orderDetail['product_quantity'] === (int) $quantity
            ) {
                return;
            }
        }

        throw new RuntimeException(
            sprintf('Order was expected to have "%d" products "%s" in it.', $quantity, $productReference)
        );
    }

    /**
     * @Given order :orderReference does not have any invoices
     */
    public function orderDoesNotHaveAnyInvoices($orderReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        if ($order->hasInvoice()) {
            throw new RuntimeException('Order should not have any invoices');
        }
    }

    /**
     * @Then order :orderReference should have invoice
     */
    public function orderShouldHaveInvoice($orderReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        if (false === $order->hasInvoice()) {
            throw new RuntimeException(sprintf('Order "%s" should have invoice', $orderReference));
        }
    }

    protected function getCurrentCartOrder()
    {
        $cart = $this->getCurrentCart();
        if (null === $cart) {
            throw new Exception('Current cart was not initialized');
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
