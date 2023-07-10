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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\CommandHandler;

use Configuration;
use Exception;
use Hook;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\DuplicateProductInOrderInvoiceException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\UpdateProductInOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use Product;
use StockAvailable;
use Validate;

/**
 * @internal
 */
#[AsCommandHandler]
final class UpdateProductInOrderHandler extends AbstractOrderCommandHandler implements UpdateProductInOrderHandlerInterface
{
    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @var OrderDetailUpdater
     */
    private $orderDetailUpdater;

    /**
     * UpdateProductInOrderHandler constructor.
     *
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     * @param OrderDetailUpdater $orderDetailUpdater
     * @param ContextStateManager $contextStateManager
     */
    public function __construct(
        OrderProductQuantityUpdater $orderProductQuantityUpdater,
        OrderDetailUpdater $orderDetailUpdater,
        ContextStateManager $contextStateManager
    ) {
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
        $this->orderDetailUpdater = $orderDetailUpdater;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        try {
            $order = $this->getOrder($command->getOrderId());

            $this->setOrderContext($this->contextStateManager, $order);

            $orderDetail = new OrderDetail($command->getOrderDetailId());
            $orderInvoice = null;
            if (!empty($command->getOrderInvoiceId())) {
                $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
            }

            // Check fields validity
            $this->assertProductCanBeUpdated($command, $orderDetail, $order, $orderInvoice);
            $this->assertProductNotDuplicate($order, $orderDetail, $orderInvoice);

            // Update current OrderDetail with new price (the object will be updated by reference)
            $this->orderDetailUpdater->updateOrderDetail(
                $orderDetail,
                $order,
                $command->getPriceTaxExcluded(),
                $command->getPriceTaxIncluded()
            );

            // We also need to update all identical OrderDetails to be sure that Cart will get the correct price
            $this->orderDetailUpdater->updateOrderDetailsForProduct(
                $order,
                (int) $orderDetail->product_id,
                (int) $orderDetail->product_attribute_id,
                $command->getPriceTaxExcluded(),
                $command->getPriceTaxIncluded()
            );

            // Update invoice, quantity and amounts
            $order = $this->orderProductQuantityUpdater->update($order, $orderDetail, $command->getQuantity(), $orderInvoice);

            Hook::exec('actionOrderEdited', ['order' => $order]);
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param UpdateProductInOrderCommand $command
     * @param OrderDetail $orderDetail
     * @param Order $order
     * @param OrderInvoice|null $orderInvoice
     *
     * @throws OrderException
     */
    private function assertProductCanBeUpdated(
        UpdateProductInOrderCommand $command,
        OrderDetail $orderDetail,
        Order $order,
        OrderInvoice $orderInvoice = null
    ) {
        // assert product exists
        $product = new Product($orderDetail->product_id);
        if ($product->id !== (int) $orderDetail->product_id) {
            throw new CannotFindProductInOrderException('You cannot edit the price of a product that no longer exists in your catalog.');
        }

        if (!Validate::isLoadedObject($orderDetail)) {
            throw new OrderException('The Order Detail object could not be loaded.');
        }

        if (null !== $orderInvoice && !Validate::isLoadedObject($orderInvoice)) {
            throw new OrderException('The invoice object cannot be loaded.');
        }

        if (!Validate::isLoadedObject($order)) {
            throw new OrderException('The order object cannot be loaded.');
        }

        if ($orderDetail->id_order != $order->id) {
            throw new OrderException('You cannot edit the order detail for this order.');
        }

        // We can't edit a delivered order
        if ($order->hasBeenDelivered()) {
            throw new CannotEditDeliveredOrderProductException('You cannot edit a delivered order.');
        }

        if (null !== $orderInvoice && $orderInvoice->id_order != $order->id) {
            throw new OrderException('You cannot use this invoice for the order');
        }

        if ($command->getPriceTaxIncluded()->isNegative() || $command->getPriceTaxExcluded()->isNegative()) {
            throw new OrderException('Invalid price');
        }

        if (!Validate::isUnsignedInt($command->getQuantity())) {
            throw new OrderException('Invalid quantity');
        }

        //check if product is available in stock
        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct(
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $orderDetail->id_shop
            );
            $quantityDiff = $command->getQuantity() - (int) $orderDetail->product_quantity;

            if ($quantityDiff > $availableQuantity) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param OrderInvoice|null $orderInvoice
     *
     * @throws DuplicateProductInOrderInvoiceException
     */
    private function assertProductNotDuplicate(Order $order, OrderDetail $orderDetail, ?OrderInvoice $orderInvoice = null): void
    {
        // If the OrderDetail's invoice is not changed no reason to check
        if (null === $orderInvoice || (int) $orderInvoice->id === (int) $orderDetail->id_order_invoice) {
            return;
        }

        // If no multi invoice possible no reason to check
        if (!$order->hasInvoice()) {
            return;
        }

        $invoicesContainingProduct = [];
        foreach ($order->getOrderDetailList() as $orderDetailData) {
            if ((int) $orderDetail->id === (int) $orderDetailData['id_order_detail']) {
                continue;
            }
            if ((int) $orderDetail->product_id !== (int) $orderDetailData['product_id']) {
                continue;
            }
            if ((int) $orderDetail->product_attribute_id !== (int) $orderDetailData['product_attribute_id']) {
                continue;
            }
            $invoicesContainingProduct[] = (int) $orderDetailData['id_order_invoice'];
        }

        // No invoices contain the product it's fine
        if (empty($invoicesContainingProduct)) {
            return;
        }

        // The newly assigned invoice already contains this product, this it not possible
        if (in_array((int) $orderInvoice->id, $invoicesContainingProduct)) {
            $invoiceNumber = $orderInvoice->getInvoiceNumberFormatted((int) Configuration::get('PS_LANG_DEFAULT'), $order->id_shop);
            throw new DuplicateProductInOrderInvoiceException($invoiceNumber, 'You cannot add this product in this invoice as it is already present');
        }
    }
}
