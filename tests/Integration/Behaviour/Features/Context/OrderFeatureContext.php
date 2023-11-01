<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context;

use AdminKernel;
use Behat\Gherkin\Node\TableNode;
use Configuration;
use Exception;
use Order;
use OrderCarrier;
use OrderCartRule;
use PHPUnit\Framework\Assert as Assert;
use RuntimeException;
use Tests\Integration\Utility\PaymentModuleFake;
use Tests\Resources\TestCase\ExtendedTestCaseMethodsTrait;

class OrderFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;
    use ExtendedTestCaseMethodsTrait;

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
                throw new Exception(sprintf('Invalid payment module: %s' . $paymentModuleName));
        }

        // need to boot kernel for usage in $paymentModule->validateOrder()
        global $kernel;
        $previousKernel = $kernel;
        $kernel = new AdminKernel('test', true);
        $kernel->boot();

        // need to update secret_key in order to get payment working
        $cart = $this->getCurrentCart();
        $cart->secure_key = md5('xxx');
        $cart->update();
        $paymentModule->validateOrder(
            $cart->id,
            (int) Configuration::get('PS_OS_CHEQUE'), // PS_OS_PAYMENT for payment-validated order
            0,
            'Unknown',
            null,
            [],
            null,
            false,
            $cart->secure_key
        );
        /** @var Order $order */
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $total));
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $total));
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedTotal, $total));
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
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $expectedCount, $count));
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
            throw new Exception(sprintf('Undefined order cart rule on position #%s', $position));
        }
        $orderCartRule = new OrderCartRule($orderCartRulesData[$position - 1]['id_order_cart_rule']);
        if ((float) $discountTaxIncluded != (float) $orderCartRule->value) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $discountTaxIncluded, $orderCartRule->value));
        }
        if ((float) $discountTaxExcluded != (float) $orderCartRule->value_tax_excl) {
            throw new RuntimeException(sprintf('Expects %s, got %s instead', $discountTaxExcluded, $orderCartRule->value_tax_excl));
        }
    }

    /**
     * @Then order :reference should have following details:
     */
    public function checkOrderDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $orderData = $table->getRowsHash();

        $order = new Order($orderId);
        foreach ($orderData as $orderField => $orderValue) {
            Assert::assertEquals(
                (float) $orderValue,
                $order->{$orderField},
                sprintf(
                    'Invalid order field %s, expected %s instead of %s',
                    $orderField,
                    $orderValue,
                    $order->{$orderField}
                )
            );
        }
    }

    /**
     * @Then order :reference should have following tax details:
     */
    public function checkOrderTaxDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $taxDetailsData = $table->getColumnsHash();

        $order = new Order($orderId);
        $orderProductsTaxDetails = $order->getProductTaxesDetails();
        // Check that the number of rows match
        Assert::assertLessThanOrEqual(
            count($orderProductsTaxDetails),
            count($taxDetailsData),
            sprintf(
                'Invalid number of tax details, expected at least %d instead of %d',
                count($taxDetailsData),
                count($orderProductsTaxDetails)
            )
        );

        foreach ($taxDetailsData as $taxDetailsIndex => $expectedTaxDetails) {
            $productsTaxDetails = $orderProductsTaxDetails[$taxDetailsIndex];
            foreach ($expectedTaxDetails as $taxField => $taxValue) {
                $this->assertEqualsWithEpsilon(
                    (float) $taxValue,
                    (float) $productsTaxDetails[$taxField],
                    sprintf(
                        'Invalid order tax field %s, expected %s instead of %s',
                        $taxField,
                        $taxValue,
                        (float) $productsTaxDetails[$taxField]
                    )
                );
            }
        }
    }

    /**
     * @Then order :reference should have no tax details
     */
    public function checkOrderHasNoTaxDetails(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $order = new Order($orderId);
        $orderProductsTaxDetails = $order->getProductTaxesDetails();
        Assert::assertEmpty($orderProductsTaxDetails, 'The order should have no tax details');
    }

    /**
     * @Then order :reference carrier should have following details:
     */
    public function checkOrderCarrierDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $orderCarrierData = $table->getRowsHash();

        $order = new Order($orderId);
        $orderCarrier = new OrderCarrier($order->getIdOrderCarrier());
        foreach ($orderCarrierData as $orderCarrierField => $orderCarrierValue) {
            Assert::assertEquals(
                (float) $orderCarrierValue,
                $orderCarrier->{$orderCarrierField},
                sprintf(
                    'Invalid order carrier field %s, expected %s instead of %s',
                    $orderCarrierField,
                    $orderCarrierValue,
                    $orderCarrier->{$orderCarrierField}
                )
            );
        }
    }

    /**
     * @Then order :reference should have :carrierReference as a carrier
     *
     * @param string $orderReference
     * @param string $carrierReference
     */
    public function checkOrderCarrier(string $orderReference, string $carrierReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $carrierId = (int) SharedStorage::getStorage()->get($carrierReference);
        $order = new Order($orderId);

        if ((int) $order->id_carrier === 0) {
            throw new RuntimeException(sprintf(
                'Order %s has no carrier defined',
                $orderReference
            ));
        }
        if ((int) $order->id_carrier !== $carrierId) {
            throw new RuntimeException(sprintf(
                'Order %s should have %s as a carrier, expected id_carrier to be %d but is %d instead',
                $orderReference,
                $carrierReference,
                $carrierId,
                (int) $order->id_carrier
            ));
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

    /**
     * @Then order :reference should have :paymentModuleName payment method
     *
     * @param string $reference
     * @param string $paymentModuleName
     */
    public function createdOrderShouldHavePaymentMethod(string $reference, string $paymentModuleName)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        $order = new Order($orderId);

        // todo: think about a way to get paymentModuleName from domain classes
        if ($order->module !== $paymentModuleName) {
            throw new RuntimeException(sprintf('Order should have "%s" payment method, but has "%s" instead.', $paymentModuleName, $order->payment));
        }
    }
}
