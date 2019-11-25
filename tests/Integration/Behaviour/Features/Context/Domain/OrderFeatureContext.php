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

use Order;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopCollection;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use Product;
use Context;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    private const ORDER_STATUS_MAP = [
        1 => 'Awaiting bank wire payment',
        5 => 'Delivered'
    ];

    /**
     * @When I add order :orderReference from cart :cartReference with :paymentModuleName payment method and :orderStatus order status
     */
    public function placeOrderWithPaymentMethodAndOrderStatus(
        $orderReference,
        $cartReference,
        $paymentModuleName,
        $orderStatus
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
                (int) SharedStorage::getStorage()->get($cartReference)->id,
                (int) Context::getContext()->employee->id,
                '',
                $paymentModuleName,
                $orderStatusId
            )
        );

        SharedStorage::getStorage()->set($orderReference, new Order($orderId->getValue()));
    }

    /**
     * @When I add :quantity products with reference :productReference, price :price and free shipping to order :orderReference with new invoice
     */
    public function addProductToOrderWithFreeShippingAndNewInvoice(
        $quantity,
        $productReference,
        $price,
        $orderReference
    ) {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $productId = Product::getIdByReference($productReference);

        $this->getCommandBus()->handle(
            AddProductToOrderCommand::withNewInvoice(
                (int) $order->id,
                (int) $productId,
                0,
                (float) $price,
                (float) $price,
                (int) $quantity,
                true
            )
        );
    }

    /**
     * @When I generate invoice for :invoiceReference order
     */
    public function generateOrderInvoice($orderReference)
    {
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $this->getCommandBus()->handle(
            new GenerateInvoiceCommand((int) $order->id)
        );
    }

    /**
     * @When I update orders :references to status :status
     * @throws OrderException
     */
    public function iUpdateOrdersToStatus(string $references, string $status)
    {
        /** @var string[] $references */
        $references = explode(',', $references);
        $ordersIds = [];
        foreach ($references as $orderReference) {
            $orderId = $this->getOrderId($orderReference);
            if ($orderId) {
                $ordersIds[] = $orderId;
            }
        }

        $statusId = $this->getOrderStatusId($status);
        $this->getCommandBus()->handle(
            new BulkChangeOrderStatusCommand(
                $ordersIds, $statusId
            )
        );
    }


    /**
     * @Then order :reference has status :status
     */
    public function orderHasStatus(string $reference, string $status)
    {
        /** @var PrestaShopCollection|Order[] $orderDetails */
        $orders = Order::getByReference($reference);
        /** @var Order $order */
        $order = $orders->getFirst();
        /** @var OrderState $currentOrderState */
        $currentOrderStateId = (int) $order->getCurrentState();
        $statusId = $this->getOrderStatusId($status);
        if ($currentOrderStateId !== $statusId) {
            throw new RuntimeException(
                'After changing order status id should be ['.$statusId.'] but received ['.$currentOrderStateId.']'
            );
        }
    }


    /**
     * @Given there is existing order with reference :reference
     */
    public function thereIsExistingOrderWithReference(string $reference)
    {
        /** @var PrestaShopCollection $orders */
        $orders = Order::getByReference($reference);
        if ($orders->count() === 0) {
            throw new RuntimeException(
                'There is no order with reference ['.$reference.']'
            );
        }
    }

    /**
     * @When I update order :reference to status :status
     */
    public function iUpdateOrderToStatus(string $reference, string $status)
    {
        $statusId = $this->getOrderStatusId($status);
        $orderId = $this->getOrderId($reference);
        $this->getCommandBus()->handle(
            new UpdateOrderStatusCommand(
                $orderId,
                $statusId
            )
        );
    }

    /**
     * @param string $reference
     * @return int
     */
    private function getOrderId(string $reference)
    {
        /** @var PrestaShopCollection $ordersCollection */
        $ordersCollection = Order::getByReference($reference);
        $reference = $ordersCollection->getFirst();
        if ($reference) {
            $orderId = (int)$reference->id;
            return $orderId;
        } else {
            throw new RuntimeException('Order with reference [' . $reference . '] does not exist');
        }
    }

    /**
     * @param string $status
     * @return int
     */
    private function getOrderStatusId(string $status)
    {
        $orderStatusMapFlipped = array_flip(self::ORDER_STATUS_MAP);
        if (isset($orderStatusMapFlipped[$status])) {
            /** @var int $statusId */
            $statusId = $orderStatusMapFlipped[$status];
            return $statusId;
        } else {
            throw new RuntimeException('Invalid status ['.$status.']');
        }
    }

}
