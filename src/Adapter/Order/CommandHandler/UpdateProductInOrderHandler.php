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

use Cart;
use Exception;
use Hook;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotEditDeliveredOrderProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotUpdateProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\Command\UpdateProductInOrderCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Product\CommandHandler\UpdateProductInOrderHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use Product;
use StockAvailable;
use Tools;
use Validate;

/**
 * @internal
 */
final class UpdateProductInOrderHandler extends AbstractOrderHandler implements UpdateProductInOrderHandlerInterface
{
    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * UpdateProductInOrderHandler constructor.
     *
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     */
    public function __construct(
        OrderProductQuantityUpdater $orderProductQuantityUpdater
    ) {
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductInOrderCommand $command)
    {
        try {
            $order = $this->getOrder($command->getOrderId());
            $cart = Cart::getCartByOrderId($order->id);
            $computingPrecision = $this->getPrecisionFromCart($cart);

            $orderDetail = new OrderDetail($command->getOrderDetailId());
            $orderInvoice = null;
            if (!empty($command->getOrderInvoiceId())) {
                $orderInvoice = new OrderInvoice($command->getOrderInvoiceId());
            }

            // Check fields validity
            $this->assertProductCanBeUpdated($command, $orderDetail, $order, $orderInvoice);

            // @todo: use https://github.com/PrestaShop/decimal for price computations
            // Update OrderDetail prices
            $productQuantity = $command->getQuantity();
            $unitProductPriceTaxIncl = (float) $command->getPriceTaxIncluded()->round($computingPrecision);
            $unitProductPriceTaxExcl = (float) $command->getPriceTaxExcluded()->round($computingPrecision);

            $orderDetail->unit_price_tax_incl = $unitProductPriceTaxIncl;
            $orderDetail->unit_price_tax_excl = $unitProductPriceTaxExcl;
            $orderDetail->total_price_tax_incl = Tools::ps_round($unitProductPriceTaxIncl * $productQuantity, $computingPrecision);
            $orderDetail->total_price_tax_excl = Tools::ps_round($unitProductPriceTaxExcl * $productQuantity, $computingPrecision);
            if (!$orderDetail->save()) {
                throw new OrderException('An error occurred while editing the product line.');
            }

            if (!($cart instanceof Cart)) {
                throw new OrderException('Cart linked to the order cannot be found.');
            }
            $product = $this->getProduct(new ProductId((int) $orderDetail->product_id), (int) $order->id_lang);
            $combination = $this->getCombination((int) $orderDetail->product_attribute_id);

            $this->updateSpecificPrice(
                $command->getPriceTaxIncluded(),
                $command->getPriceTaxExcluded(),
                $order,
                $product,
                $combination
            );

            // update order details
            $this->updateOrderDetailsWithSameProduct(
                $order,
                $orderDetail,
                $computingPrecision
            );

            // Update quantity and amounts
            $order = $this->orderProductQuantityUpdater->update($order, $orderDetail, $productQuantity, $orderInvoice);

            Hook::exec('actionOrderEdited', ['order' => $order]);
        } catch (Exception $e) {
            throw $e;
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
            throw new CannotUpdateProductInOrderException('You cannot edit the price of a product that no longer exists in your catalog.');
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

        if (!is_array($command->getQuantity())
            && !Validate::isUnsignedInt($command->getQuantity())
        ) {
            throw new OrderException('Invalid quantity');
        }

        // @todo: check if quantity can be array
//        if (is_array($command->getQuantity())) {
//            foreach ($command->getQuantity() as $qty) {
//                if (!Validate::isUnsignedInt($qty)) {
//                    throw new OrderException('Invalid quantity');
//                }
//            }
//        }

        //check if product is available in stock
        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($orderDetail->product_id, $orderDetail->product_attribute_id);
            $quantityDiff = $command->getQuantity() - (int) $orderDetail->product_quantity;

            if ($quantityDiff > $availableQuantity) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }
}
