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

use Exception;
use Order;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopDatabaseException;
use PrestaShopException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use Product;
use Context;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    const ORDER_STATUS_MAP = [
        'Awaiting bank wire payment' => 1,
        'Delivered' => 5,
    ];

    /**
     * @param $orderReference
     * @param $cartReference
     * @param $paymentModuleName
     * @param $orderStatus
     * @throws CartConstraintException
     * @throws InvalidEmployeeIdException
     * @throws OrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
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
     * @Given there are :countOfOrders existing orders
     * @throws Exception
     */
    public function thereAreExistingOrders(int $countOfOrders)
    {
        /** @var array $ordersWithInformations */
        $ordersWithInformations = Order::getOrdersWithInformations($countOfOrders);
        $countOfOrdersFromDb = count($ordersWithInformations);
        if ($countOfOrders !== $countOfOrdersFromDb) {
            throw new Exception(
                'There are less orders than expected ['.$countOfOrders.'] actual ['.$countOfOrdersFromDb.']'
            );
        }
    }

    /**
     * @When I update :countOfOrders orders to status :status
     * @throws OrderException
     */
    public function iUpdateOrdersToStatus(string $status, int $countOfOrders)
    {
        /** @var array $ordersWithInformations */
        $ordersWithInformations = Order::getOrdersWithInformations($countOfOrders);

        $orderIds = [];
        foreach ($ordersWithInformations as $orderWithInformations) {
            $orderIds[] = (int) $orderWithInformations['id_order'];
        }

        $statusId = self::ORDER_STATUS_MAP[$status];

        $this->getCommandBus()->handle(
            new BulkChangeOrderStatusCommand(
                $orderIds, $statusId
            )
        );
    }

    /**
     * @Then each of :countOfOrders orders should contain status :status
     * @param int $countOfOrders
     * @param string $status
     * @throws Exception
     */
    public function eachOfOrdersShouldContainStatus(int $countOfOrders, string $status)
    {
        /** @var array $ordersWithInformations */
        $ordersWithInformations = Order::getOrdersWithInformations($countOfOrders);

        foreach ($ordersWithInformations as $orderWithInformation) {
            $currentOrderStateId = $orderWithInformation['current_state'];
            $currentOrderState = array_search($currentOrderStateId, self::ORDER_STATUS_MAP);
            if ($currentOrderState !== $status) {
                throw new Exception(
                    'After changing order status id should be ['.$status.'] but received ['.$currentOrderState.']'
                );
            }
        }
    }

    /**
     * @When I update order :orderId to status :status
     */
    public function iUpdateOrderToStatus(int $orderId, string $status)
    {
        $statusId = self::ORDER_STATUS_MAP[$status];

        $this->getCommandBus()->handle(
            new UpdateOrderStatusCommand(
                $orderId,
                $statusId
            )
        );
    }

}
