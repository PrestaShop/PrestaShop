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
use Order;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use RuntimeException;
use stdClass;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    private const ORDER_STATUS_MAP = [
        1 => 'Awaiting bank wire payment',
        5 => 'Delivered',
    ];

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
     * @When I add order :orderReference from cart :cartReference with :paymentModuleName payment method and :orderStatus order status
     *
     * @param string $orderReference
     * @param string $cartReference
     * @param string $paymentModuleName
     * @param string $orderStatus
     *
     * @throws OrderException
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws RuntimeException
     * @throws CartConstraintException
     * @throws InvalidEmployeeIdException
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
     *
     * @param int $quantity
     * @param string $productReference
     * @param int $price
     * @param string $orderReference
     */
    public function addProductsToOrderWithFreeShippingAndNewInvoice(
        int $quantity,
        string $productReference,
        int $price,
        string $orderReference
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
     * @When I add products with new invoice and the following properties:
     *
     * @param TableNode $table
     *
     * @throws RuntimeException
     */
    public function iAddProductsWithNewInvoiceAndTheFollowingProperties(TableNode $table)
    {
        $data = $this->extractFirstRowFromProperties($table);

        $this->getCommandBus()->handle(
            AddProductToOrderCommand::withNewInvoice(
                (int) $data['id_order'],
                (int) $data['id_product'],
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
        $orders = Order::getByReference($orderReference);
        /** @var Order $order */
        $order = $orders->getFirst();

        $this->getCommandBus()->handle(
            new GenerateInvoiceCommand((int) $order->id)
        );
    }

    /**
     * @When I update orders with ids :references status to :status
     *
     * @param string $orderIdsString
     * @param string $status
     *
     * @throws OrderException
     * @throws RuntimeException
     */
    public function iUpdateOrdersWithIdsStatusTo(string $orderIdsString, string $status)
    {
        /** @var string[] $orderIdsString */
        $orderIdsString = explode(',', $orderIdsString);
        $ordersIds = [];
        foreach ($orderIdsString as $orderIdString) {
            $ordersIds[] = (int) $orderIdString;
        }

        $statusId = $this->getOrderStatusIdFromMap($status);
        $this->getCommandBus()->handle(
            new BulkChangeOrderStatusCommand(
                $ordersIds, $statusId
            )
        );
    }

    /**
     * @Then order with id :orderId has status :status
     *
     * @param int $orderId
     * @param string $status
     *
     * @throws RuntimeException
     */
    public function orderWithIdHasStatus(int $orderId, string $status)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderState $currentOrderState */
        $currentOrderStateId = $orderForViewing->getHistory()->getCurrentOrderStatusId();
        $statusId = $this->getOrderStatusIdFromMap($status);
        if ($currentOrderStateId !== $statusId) {
            throw new RuntimeException(
                'After changing order status id should be [' . $statusId . '] but received [' . $currentOrderStateId . ']'
            );
        }
    }

    /**
     * @Given there is existing order with id :orderId
     *
     * @param int $orderId
     */
    public function thereIsExistingOrderWithId(int $orderId)
    {
        $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
    }

    /**
     * @When I update order with id :orderId to status :status
     *
     * @param int $orderId
     * @param string $status
     *
     * @throws RuntimeException
     */
    public function iUpdateOrderWithIdToStatus(int $orderId, string $status)
    {
        $statusId = $this->getOrderStatusIdFromMap($status);
        $this->getCommandBus()->handle(
            new UpdateOrderStatusCommand(
                $orderId,
                $statusId
            )
        );
    }

    /**
     * @param string $status
     *
     * @return int
     *
     * @throws RuntimeException
     */
    private function getOrderStatusIdFromMap(string $status)
    {
        $orderStatusMapFlipped = array_flip(self::ORDER_STATUS_MAP);
        if (isset($orderStatusMapFlipped[$status])) {
            /** @var int $statusId */
            $statusId = $orderStatusMapFlipped[$status];

            return $statusId;
        }
        throw new RuntimeException('Invalid status [' . $status . ']');
    }

    /**
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
}
