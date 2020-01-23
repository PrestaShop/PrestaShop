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

use Behat\Gherkin\Node\TableNode;
use Order;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\BulkChangeOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderShippingDetailsCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\AddProductToOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCarrierForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopException;
use RuntimeException;
use stdClass;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command\GenerateInvoiceCommand;
use Product;
use Context;

class OrderFeatureContext extends AbstractDomainFeatureContext
{
    private const ORDER_STATUS_MAP = [
        1 => 'Awaiting bank wire payment',
        5 => 'Delivered',
    ];

    private const CARRIER_MAP = [
        1 => '0',
        2 => 'My carrier',
    ];

    /**
     * @BeforeScenario
     */
    public function before()
    {
        // needed because if no controller defined then CONTEXT_ALL is selected and exception is thrown
        $adminControllerTestDouble = new stdClass();
        $adminControllerTestDouble->controller_type = 'admin';
        Context::getContext()->controller = $adminControllerTestDouble;
    }

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
     * @When I update orders :references to status :status
     *
     * @param string $orderIdsString
     * @param string $status
     *
     * @throws OrderException
     */
    public function iUpdateOrdersToStatus(string $orderIdsString, string $status)
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
     * @Then order :orderId has status :status
     *
     * @param int $orderId
     * @param string $status
     */
    public function orderHasStatus(int $orderId, string $status)
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
     * @When I update order :orderId to status :status
     *
     * @param int $orderId
     * @param string $status
     */
    public function iUpdateOrderToStatus(int $orderId, string $status)
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
     * @When I update order :reference Tracking number to :trackingNumber and Carrier to :carrier
     *
     * @param int $orderId
     * @param string $trackingNumber
     * @param string $carrier
     */
    public function iUpdateOrderTrackingNumberToAndCarrierTo(int $orderId, string $trackingNumber, string $carrier)
    {
        $oldOrderCarrierId = $this->getCarrierIdFromMap($carrier);
        $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        $newCarrierId = $this->getCarrierIdFromMap($carrier);

        $this->getCommandBus()->handle(
            new UpdateOrderShippingDetailsCommand(
                $orderId,
                $oldOrderCarrierId,
                $newCarrierId,
                $trackingNumber
            )
        );
    }

    /**
     * @Then order :orderId has Tracking number :trackingNumber
     *
     * @param int $orderId
     * @param string $trackingNumber
     */
    public function orderHasTrackingNumber(int $orderId, string $trackingNumber)
    {
        $orderCarriersForViewing = $this->getOrderCarriersForViewing($orderId);
        $orderTrackingNumberFromDb = $orderCarriersForViewing[0]->getTrackingNumber();

        if ($trackingNumber !== $orderTrackingNumberFromDb) {
            $msg = 'Order [' . $orderId . '] tracking number is not equal to [' . $trackingNumber . '] ';
            $msg .= 'Received [' . $orderTrackingNumberFromDb . '] ';
            throw new RuntimeException($msg);
        }
    }

    /**
     * @Then order :orderId has Carrier :carrier
     *
     * @param string $orderId
     * @param string $carrier
     */
    public function orderHasCarrier(string $orderId, string $carrier)
    {
        $carrierId = $this->getCarrierIdFromMap($carrier);
        /** @var OrderCarrierForViewing[] $orderCarriersForViewing */
        $orderCarriersForViewing = $this->getOrderCarriersForViewing($orderId);
        $carrierIdFromDb = $orderCarriersForViewing[0]->getCarrierId();

        if ($carrierId !== $carrierIdFromDb) {
            $msg = 'Order [' . $orderId . '] carrier id is not equal to [' . $carrierId . '] ';
            $msg .= 'Received [' . $carrierIdFromDb . '] ';
            throw new RuntimeException($msg);
        }
    }

    /**
     * @param string $status
     *
     * @return int
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
     * @param string $carrier
     *
     * @return int
     */
    private function getCarrierIdFromMap(string $carrier)
    {
        $carrierMapFlipped = array_flip(self::CARRIER_MAP);
        if (isset($carrierMapFlipped[$carrier])) {
            /** @var int $carrierId */
            $carrierId = $carrierMapFlipped[$carrier];

            return $carrierId;
        }
        throw new RuntimeException('Invalid carrier [' . $carrier . ']');
    }

    /**
     * @When I add payment to order with id :orderId with the following properties:
     *
     * @param int $orderId
     * @param TableNode $table
     */
    public function iAddPaymentToOrderWithIdWithTheFollowingProperties(int $orderId, TableNode $table)
    {
        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        $this->getCommandBus()->handle(
            new AddPaymentCommand(
                $orderId,
                $data['date'],
                $data['payment_method'],
                $data['amount'],
                (int) $data['id_currency'],
                $data['id_invoice'],
                $data['transaction_id']
            )
        );
    }

    /**
     * @When I add payment to order id :orderId exception is thrown with the following properties:
     */
    public function iAddPaymentToOrderIdExceptionIsThrownWithTheFollowingProperties(int $orderId, TableNode $table)
    {
        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        try {
            $this->getCommandBus()->handle(
                new AddPaymentCommand(
                    $orderId,
                    $data['date'],
                    $data['payment_method'],
                    $data['amount'],
                    (int) $data['id_currency'],
                    $data['id_invoice'],
                    $data['transaction_id']
                )
            );
        } catch (PrestaShopException $exception) {
            $msg = $exception->getMessage();
            $expectedMsg = 'Property Order->total_paid_real is not valid';
            if ($msg !== 'Property Order->total_paid_real is not valid') {
                throw new RuntimeException(sprintf(
                    'Not expected exception is thrown "%s" but "%s" was expected',
                    $msg,
                    $expectedMsg));
            }
        }
    }

    /**
     * @Then if I query order with id :orderId payments I should get an Order with properties:
     *
     * @param int $orderId
     * @param TableNode $table
     */
    public function ifIQueryOrderWithIdPaymentsIShouldGetAnOrderWithProperties(int $orderId, TableNode $table)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();

        if (count($orderPaymentForViewingArray) == 0) {
            throw new RuntimeException('Order [' . $orderId . '] has no payments for viewing');
        }

        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $orderPaymentForViewingArray[0];

        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        $orderPaymentDateFromDb = $orderPaymentForViewing->getDate()->format('Y-m-d H:i:s');
        $orderPaymentDate = $data['date'];
        if ($orderPaymentDate !== $orderPaymentDateFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" payment date is not the same as "%s", but "%s" was expected',
                $orderId,
                $orderPaymentDateFromDb,
                $orderPaymentDate
            ));
        }

        $paymentMethodFromDb = $orderPaymentForViewing->getPaymentMethod();
        $orderPaymentMethod = $data['payment_method'];
        if ($orderPaymentMethod !== $paymentMethodFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" payment method is not the same as "%s", but "%s" was expected',
                $orderId,
                $paymentMethodFromDb,
                $orderPaymentMethod
            ));
        }

        $transactionIdFromDb = $orderPaymentForViewing->getTransactionId();
        $transactionId = $data['transaction_id'];
        if ($transactionId !== $transactionIdFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" transaction id is not the same as "%s", but "%s" was expected',
                $orderId,
                $transactionIdFromDb,
                $transactionId
            ));
        }

        //       | date                | payment_method    | transaction_id              | id_currency | amount | id_invoice |\
        $amountFromDb = $orderPaymentForViewing->getAmount();
        $amount = $data['amount'];
        if ($amount !== $amountFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" amount is not the same as "%s", but "%s" was expected',
                $orderId,
                $amountFromDb,
                $amount
            ));
        }

        $invoiceNumberFromDb = $orderPaymentForViewing->getInvoiceNumber();
        $invoiceId = $data['id_invoice'];
        if ($invoiceId !== $invoiceNumberFromDb && $invoiceNumberFromDb != '') {
            throw new RuntimeException(sprintf(
                'Order "%s" invoice id is not the same as "%s", but "%s" was expected',
                $orderId,
                $invoiceNumberFromDb,
                $invoiceId
            ));
        }
    }

    /**
     * @param int $orderId
     *
     * @return array|OrderCarrierForViewing[]
     */
    private function getOrderCarriersForViewing(int $orderId)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderCarrierForViewing[] $orderCarriers */
        $orderCarriersForViewing = $orderForViewing->getShipping()->getCarriers();

        if (count($orderCarriersForViewing) == 0) {
            $msg = 'Order [' . $orderId . '] has no carriers';
            throw new RuntimeException($msg);
        }

        return $orderCarriersForViewing;
    }
}
