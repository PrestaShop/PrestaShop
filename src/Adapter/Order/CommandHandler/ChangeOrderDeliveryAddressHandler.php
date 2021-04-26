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

use Address;
use Cart;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductsComparator;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductUpdate;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\OrderAmountUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailUpdater;
use PrestaShop\PrestaShop\Adapter\Order\OrderProductQuantityUpdater;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\ChangeOrderDeliveryAddressCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\CommandHandler\ChangeOrderDeliveryAddressHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use Validate;

/**
 * @internal
 */
final class ChangeOrderDeliveryAddressHandler extends AbstractOrderCommandHandler implements ChangeOrderDeliveryAddressHandlerInterface
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var OrderDetailUpdater
     */
    private $orderDetailTaxUpdater;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var OrderProductQuantityUpdater
     */
    private $orderProductQuantityUpdater;

    /**
     * @param OrderAmountUpdater $orderAmountUpdater
     * @param OrderDetailUpdater $orderDetailTaxUpdater
     * @param ContextStateManager $contextStateManager
     * @param OrderProductQuantityUpdater $orderProductQuantityUpdater
     */
    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        OrderDetailUpdater $orderDetailTaxUpdater,
        ContextStateManager $contextStateManager,
        OrderProductQuantityUpdater $orderProductQuantityUpdater
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderDetailTaxUpdater = $orderDetailTaxUpdater;
        $this->contextStateManager = $contextStateManager;
        $this->orderProductQuantityUpdater = $orderProductQuantityUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(ChangeOrderDeliveryAddressCommand $command)
    {
        $order = $this->getOrder($command->getOrderId());
        $address = new Address($command->getNewDeliveryAddressId()->getValue());

        $cart = Cart::getCartByOrderId($order->id);

        if (!Validate::isLoadedObject($address)) {
            throw new OrderException('New delivery address is not valid');
        }

        $this->setCartContext($this->contextStateManager, $cart);

        try {
            $comparator = new CartProductsComparator($cart);

            $cart->updateDeliveryAddressId((int) $cart->id_address_delivery, (int) $address->id);
            $cart->setDeliveryOption([
                (int) $cart->id_address_delivery => $this->formatLegacyDeliveryOptionFromCarrierId($order->id_carrier),
            ]);
            $cart->update();

            // gift could have been added/deleted when changing delivery address
            $this->synchronizeOrderWithCart($order, $cart, $comparator);

            $order->id_address_delivery = $address->id;
            $this->orderDetailTaxUpdater->updateOrderDetailsTaxes($order);
            $this->orderAmountUpdater->update($order, $cart);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param CartProductsComparator $productsComparator
     */
    private function synchronizeOrderWithCart(
        Order $order,
        Cart $cart,
        CartProductsComparator $productsComparator
    ): void {
        $modified = $productsComparator->getModifiedProducts();
        foreach ($modified as $productUpdate) {
            $orderDetail = $this->getOrderDetail($productUpdate, $order, $cart);
            if (null === $orderDetail) {
                continue;
            }
            $quantity = $productUpdate->isCreated()
                ? $productUpdate->getDeltaQuantity()
                : $orderDetail->product_quantity + $productUpdate->getDeltaQuantity();
            $orderInvoice = $orderDetail->id_order_invoice != 0 ? new OrderInvoice($orderDetail->id_order_invoice) : null;

            $this->orderProductQuantityUpdater->update(
                $order,
                $orderDetail,
                $quantity,
                $orderInvoice,
                false
            );
        }
    }

    /**
     * @param CartProductUpdate $productUpdate
     * @param Order $order
     * @param Cart $cart
     *
     * @return OrderDetail|null
     */
    private function getOrderDetail(CartProductUpdate $productUpdate, Order $order, Cart $cart): ?OrderDetail
    {
        $combinationId = $productUpdate->getCombinationId() ? $productUpdate->getCombinationId()->getValue() : 0;
        foreach ($order->getProducts() as $product) {
            if (
                (int) $product['product_id'] === $productUpdate->getProductId()->getValue()
                && (int) $product['product_attribute_id'] === $combinationId
            ) {
                return new OrderDetail($product['id_order_detail']);
            }
        }

        foreach ($cart->getProducts() as $product) {
            if (
                (int) $product['id_product'] === $productUpdate->getProductId()->getValue()
                && (int) $product['id_product_attribute'] === $combinationId
            ) {
                $orderDetail = new OrderDetail();
                $orderDetail->createList($order, $cart, $order->getCurrentOrderState(), [$product]);

                return $orderDetail;
            }
        }

        return null;
    }
}
