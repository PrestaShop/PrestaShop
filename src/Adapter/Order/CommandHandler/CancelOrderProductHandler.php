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

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Cart;
use Configuration;
use Customization;
use Hook;
use Order;
use OrderDetail;
use OrderHistory;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\CancellationActionType;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\CancelOrderProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @internal
 */
final class CancelOrderProductHandler extends AbstractOrderCommandHandler implements CancelOrderProductHandlerInterface
{
    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * CancelOrderProductHandler constructor.
     *
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     */
    public function __construct(
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ) {
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Legacy code for product cancellation handling in order page
     */
    public function handle(CancelOrderProductCommand $command)
    {
        $order = new Order($command->getOrderId()->getValue());
        $this->checkInput($command);
        $this->checkOrderState($order);

        $cartId = Cart::getCartIdByOrderId($command->getOrderId()->getValue());
        $customizationQuantities = Customization::countQuantityByCart($cartId);
        $orderDetails = $this->getOrderDetails($command);

        $this->assertCancelableProductQuantities($orderDetails, $customizationQuantities);

        $this->cancelProducts($order, $orderDetails);

        if (empty($order->getOrderDetailList())) {
            $this->cancelOrder($order);
        }
    }

    private function getOrderDetails(CancelOrderProductCommand $command)
    {
        $productList = [];
        $customizedCancelQuantity = [];
        $productCancelQuantity = [];

        foreach ($command->getCancelledProducts() as $orderDetailId => $cancelQuantity) {
            $orderDetail = new OrderDetail($orderDetailId);
            $productList[] = $orderDetail;
            if ((int) $orderDetail->id_customization > 0) {
                $customizedCancelQuantity[$orderDetail->id_customization] = $cancelQuantity;
            } else {
                $productCancelQuantity[$orderDetail->id_order_detail] = $cancelQuantity;
            }
        }

        return [
            'productsOrderDetails' => $productList,
            'productCancelQuantity' => $productCancelQuantity,
            'customizedCancelQuantity' => $customizedCancelQuantity,
        ];
    }

    private function checkInput(CancelOrderProductCommand $command)
    {
        if (empty($command->getCancelledProducts())) {
            throw new InvalidCancelProductException(InvalidCancelProductException::NO_REFUNDS);
        }

        foreach ($command->getCancelledProducts() as $orderDetailId => $quantity) {
            if ((int) $quantity <= 0) {
                throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_QUANTITY);
            }
        }
    }

    /**
     * @param Order $order*
     */
    private function checkOrderState(Order $order)
    {
        if ($order->hasBeenPaid() || $order->hasPayments()) {
            throw new InvalidOrderStateException(
                InvalidOrderStateException::ALREADY_PAID,
                'Can not cancel product on an order which is already paid'
            );
        }
    }

    /**
     * @param Order $order
     */
    private function cancelOrder(Order $order)
    {
        $history = new OrderHistory();
        $history->id_order = (int) $order->id;
        $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $order);
        if (!$history->addWithemail()) {
            // email failure must not block order update process
            $this->logger->warning(
                $this->translator->trans(
                    'Order history email could not be sent, test your email configuration in the Advanced Parameters > E-mail section of your back office.',
                    [],
                    'Admin.Orderscustomers.Notification'
                )
            );
        }
    }

    /**
     * @param array $orderDetails
     *
     * @throws InvalidCancelProductException
     */
    private function assertCancelableProductQuantities(array $orderDetails, array $customizationQuantities)
    {
        if (empty($orderDetails['productsOrderDetails'])) {
            throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_QUANTITY, 0);
        }
        $customizationList = [];
        foreach ($orderDetails['productsOrderDetails'] as $orderDetail) {
            // check non customized product quantities
            if ((int) $orderDetail->id_customization <= 0) {
                $customizationQuantity = 0;
                $cancelQuantity = $orderDetails['productCancelQuantity'][$orderDetail->id_order_detail];
                if (array_key_exists($orderDetail->product_id, $customizationQuantities) && array_key_exists($orderDetail->product_attribute_id, $customizationQuantities[$orderDetail->product_id])) {
                    $customizationQuantity = (int) $customizationQuantities[$orderDetail->product_id][$orderDetail->product_attribute_id];
                }
                $cancellableQuantity = $orderDetail->product_quantity - $customizationQuantity - $orderDetail->product_quantity_refunded - $orderDetail->product_quantity_return;
                if ($cancellableQuantity < $cancelQuantity) {
                    throw new InvalidCancelProductException(InvalidCancelProductException::QUANTITY_TOO_HIGH, $cancellableQuantity);
                }
                continue;
            }
            // get list of customizations
            $customizationList[$orderDetail->id_customization] = $orderDetail->id_order_detail;
        }

        if (empty($customizationList)) {
            return;
        }

        $customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));

        // check customized products quantities
        foreach ($customizationList as $id_customization => $id_order_detail) {
            $qtyCancelProduct = abs($orderDetails['customizedCancelQuantity'][$id_customization]);
            $customization_quantity = $customization_quantities[$id_customization];
            if (!$qtyCancelProduct) {
                throw new InvalidCancelProductException(InvalidCancelProductException::INVALID_QUANTITY);
            }
            $cancellableQuantity = $customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned']);

            if ($qtyCancelProduct > $cancellableQuantity) {
                throw new InvalidCancelProductException(InvalidCancelProductException::QUANTITY_TOO_HIGH, $cancellableQuantity);
            }
        }
    }

    /**
     * @param Order $order
     * @param array $orderDetails
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException
     */
    private function cancelProducts(Order $order, array $orderDetails)
    {
        if (!empty($orderDetails['productsOrderDetails'])) {
            foreach ($orderDetails['productsOrderDetails'] as $orderDetail) {
                if ((int) $orderDetail->id_customization > 0) {
                    $qty_cancel_product = abs($orderDetails['customizedCancelQuantity'][$orderDetail->id_customization]);
                } else {
                    $qty_cancel_product = $orderDetails['productCancelQuantity'][$orderDetail->id_order_detail];
                }
                $newQuantity = max((int) $orderDetail->product_quantity - (int) $qty_cancel_product, 0);
                $orderInvoice = $orderDetail->id_order_invoice != 0 ? new OrderInvoice($orderDetail->id_order_invoice) : null;
                $this->orderProductQuantityUpdater->update($order, $orderDetail, $newQuantity, $orderInvoice);
                Hook::exec('actionProductCancel', ['order' => $order, 'id_order_detail' => (int) $orderDetail->id_order_detail, 'action' => CancellationActionType::CANCEL_PRODUCT], null, false, true, false, $order->id_shop);
            }
        }
    }
}
