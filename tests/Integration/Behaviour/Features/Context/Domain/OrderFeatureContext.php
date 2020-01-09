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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use AdminController;
use Behat\Gherkin\Node\TableNode;
use Context;
use FrontController;
use OrderState;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Product\Query\SearchProducts;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\OrderStateByIdChoiceProvider;
use RuntimeException;
use stdClass;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    private const ORDER_CART_RULE_FREE_SHIPPING = 'Free Shipping';

    /**
     * @BeforeScenario
     */
    public function before()
    {
        // needed because if no controller defined then CONTEXT_ALL is selected and exception is thrown
        /** @var AdminController|FrontController $adminControllerTestDouble */
        $adminControllerTestDouble = new stdClass();
        $adminControllerTestDouble->controller_type = 'admin';
        $adminControllerTestDouble->php_self = 'dummyTestDouble';
        Context::getContext()->controller = $adminControllerTestDouble;
    }

    /**
     * @Given I add order :orderReference with the following details:
     *
     * @param $orderReference
     * @param TableNode $table
     */
    public function addOrderWithTheFollowingDetails($orderReference, TableNode $table)
    {
        $testCaseData = $table->getRowsHash();

        $data = $this->mapAddOrderFromBackOfficeData($testCaseData);

        /** @var OrderId $orderId */
        $orderId = $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                $data['cartId'],
                $data['employeeId'],
                $data['orderMessage'],
                $data['paymentModuleName'],
                $data['orderStateId']
            )
        );

        SharedStorage::getStorage()->set($orderReference, $orderId->getValue());
    }

    /**
     * @When I add order :orderReference from cart :cartReference with :paymentModuleName payment method and :orderStatus order status
     *
     * @param string $orderReference
     * @param string $cartReference
     * @param string $paymentModuleName
     * @param string $orderStatus
     */
    public function placeOrderWithPaymentMethodAndOrderStatus(
        string $orderReference,
        string $cartReference,
        string $paymentModuleName,
        string $orderStatus
    ) {
        $orderStates = OrderState::getOrderStates(Context::getContext()->language->id);
        $orderStatusId = null;

        foreach ($orderStates as $state) {
            if ($state['name'] === $orderStatus) {
                $orderStatusId = (int) $state['id_order_state'];
            }
        }

        /** @var OrderId $orderId */
        $orderId = $this->getCommandBus()->handle(
            new AddOrderFromBackOfficeCommand(
                (int) SharedStorage::getStorage()->get($cartReference),
                (int) Context::getContext()->employee->id,
                '',
                $paymentModuleName,
                $orderStatusId
            )
        );

        SharedStorage::getStorage()->set($orderReference, $orderId->getValue());
    }

    /**
     * @When I add products to order :orderReference with new invoice and the following products details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addProductsToOrderWithNewInvoiceAndTheFollowingDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $data = $table->getRowsHash();
        $productName = $data['name'];
        /** @var array $productsMap */
        $productsMap = $this->getQueryBus()->handle(new SearchProducts($productName, 1));
        $productId = array_key_first($productsMap);

        if (!$productId) {
            throw new RuntimeException('Product with name "%s" does not exist', $productName);
        }

        $this->getCommandBus()->handle(
            AddProductToOrderCommand::withNewInvoice(
                $orderId,
                $productId,
                0,
                (float) $data['price'],
                (float) $data['price'],
                (int) $data['amount'],
                $data['free_shipping']
            )
        );
    }

    /**
     * @When I generate invoice for :invoiceReference order
     *
     * @param string $orderReference
     */
    public function generateOrderInvoice(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $this->getCommandBus()->handle(
            new GenerateInvoiceCommand($orderId)
        );
    }

    /**
     * @When I update orders :orderIdsString statuses to :status
     *
     * @param string $orderReferencesString
     * @param string $status
     */
    public function updateOrdersToStatuses(string $orderReferencesString, string $status)
    {
        /** @var string[] $orderReferencesString */
        $orderReferencesString = explode(',', $orderReferencesString);
        $ordersIds = [];
        foreach ($orderReferencesString as $orderReference) {
            $ordersIds[] = SharedStorage::getStorage()->get($orderReference);
        }

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $statusId = (int) $availableOrderStates[$status];

        $this->getCommandBus()->handle(
            new BulkChangeOrderStatusCommand(
                $ordersIds, $statusId
            )
        );
    }

    /**
     * @Then order :orderReference has status :status
     *
     * @param string $orderReference
     * @param string $status
     *
     * @throws RuntimeException
     */
    public function orderHasStatus(string $orderReference, string $status)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderState $currentOrderState */
        $currentOrderStateId = $orderForViewing->getHistory()->getCurrentOrderStatusId();

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $expectedStatusId = (int) $availableOrderStates[$status];

        if ($currentOrderStateId !== $expectedStatusId) {
            throw new RuntimeException(
                'After changing order status id should be [' . $expectedStatusId . '] but received [' . $currentOrderStateId . ']'
            );
        }
    }

    /**
     * @When I update order :orderReference status to :status
     *
     * @param string $orderReference
     * @param string $status
     */
    public function updateOrderStatusTo(string $orderReference, string $status)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $statusId = (int) $availableOrderStates[$status];

        $this->getCommandBus()->handle(
            new UpdateOrderStatusCommand(
                $orderId,
                $statusId
            )
        );
    }

    /**
     * @deprecated
     *
     * @param TableNode $table
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function extractFirstRowFromProperties(TableNode $table): array
    {
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Properties are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        return $data;
    }

    /**
     * @param array $testCaseData
     *
     * @return array
     */
    private function mapAddOrderFromBackOfficeData(array $testCaseData)
    {
        $data = [];
        $cartId = SharedStorage::getStorage()->get($testCaseData['cart']);
        $data['cartId'] = $cartId;
        $data['employeeId'] = Context::getContext()->employee->id;
        $data['orderMessage'] = $testCaseData['message'];
        $data['paymentModuleName'] = $testCaseData['payment module name'];

        /** @var OrderStateByIdChoiceProvider $orderStateChoiceProvider */
        $orderStateChoiceProvider = $this->getContainer()->get('prestashop.core.form.choice_provider.order_state_by_id');
        $availableOrderStates = $orderStateChoiceProvider->getChoices();
        $data['orderStateId'] = (int) $availableOrderStates[$testCaseData['status']];

        return $data;
    }

    /**
     * @Then order :reference should have :quantity products in total
     *
     * @param string $reference
     * @param int $quantity
     */
    public function assertOrderProductsQuantity(string $reference, int $quantity)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        $totalQuantity = 0;
        foreach ($orderProducts as $orderProduct) {
            $totalQuantity += $orderProduct->getQuantity();
        }

        if ($totalQuantity !== $quantity) {
            throw new RuntimeException(sprintf(
                'Order should have "%d" products, but has "%d".',
                $totalQuantity,
                $quantity
            ));
        }
    }

    /**
     * @Then order :reference should have free shipping
     *
     * @param string $reference
     */
    public function createdOrderShouldHaveFreeShipping(string $reference)
    {
        $orderId = SharedStorage::getStorage()->get($reference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderDiscountForViewing[] $orderDiscountsForViewing */
        $orderDiscountsForViewing = $orderForViewing->getDiscounts()->getDiscounts();

        foreach ($orderDiscountsForViewing as $discount) {
            if ($discount->getName() == self::ORDER_CART_RULE_FREE_SHIPPING) {
                return;
            }
        }

        throw new RuntimeException('Order should have free shipping.');
    }

    /**
     * @Then order :orderReference should have invoice
     *
     * @param string $orderReference
     */
    public function orderShouldHaveInvoice(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /* @var OrderInvoiceAddressForViewing $invoiceAddress */
        $orderForViewing->getInvoiceAddress();
    }

    /**
     * @Given order :orderReference does not have any invoices
     *
     * @param string $orderReference
     */
    public function orderDoesNotHaveAnyInvoices(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderInvoiceAddressForViewing $invoiceAddress */
        $invoiceAddress = $orderForViewing->getInvoiceAddress();
        PHPUnit_Framework_Assert::assertNotNull($invoiceAddress);
    }

    /**
     * @Given order with reference :orderReference does not contain product :productName
     *
     * @param string $orderReference
     * @param string $productName
     */
    public function orderDoesNotContainProduct(string $orderReference, string $productName)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var array $productsMap */
        $productsMap = $this->getQueryBus()->handle(new SearchProducts($productName, 1));
        $productId = array_key_first($productsMap);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        /** @var OrderProductForViewing[] $orderProducts */
        $orderProducts = $orderForViewing->getProducts()->getProducts();

        foreach ($orderProducts as $orderProduct) {
            if ($orderProduct->getId() == $productId) {
                throw new RuntimeException(
                    sprintf(
                        'Order with reference "%s" contains product with reference "%s".',
                        $orderReference,
                        $productName
                    )
                );
            }
        }
    }

    /**
     * @Then order :orderReference should contain :quantity products :productName
     *
     * @param string $orderReference
     * @param int $quantity
     * @param string $productName
     */
    public function orderContainsProductWithReference(string $orderReference, int $quantity, string $productName)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        /** @var array $productsMap */
        $productsMap = $this->getQueryBus()->handle(new SearchProducts($productName, 1));
        $productId = array_key_first($productsMap);

        if (!$productId) {
            throw new RuntimeException('Product with name "%s" does not exist', $productName);
        }

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderProductForViewing[] $products */
        $products = $orderForViewing->getProducts()->getProducts();

        $totalProductQuantity = 0;
        foreach ($products as $product) {
            if ($product->getId() === $productId) {
                $totalProductQuantity += $product->getQuantity();
            }
        }

        if ((int) $totalProductQuantity === (int) $quantity) {
            return;
        }

        throw new RuntimeException(
            sprintf(
                'Order was expected to have "%d" products "%s" in it. Instead got "%s"',
                $quantity,
                $productName,
                $totalProductQuantity
            )
        );
    }
}
