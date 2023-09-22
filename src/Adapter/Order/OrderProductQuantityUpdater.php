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

namespace PrestaShop\PrestaShop\Adapter\Order;

use Cart;
use Configuration;
use Currency;
use Customer;
use Customization;
use Db;
use Order;
use OrderDetail;
use OrderInvoice;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductsComparator;
use PrestaShop\PrestaShop\Adapter\Cart\Comparator\CartProductUpdate;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderProductRemover;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductOutOfStockException;
use Product;
use Shop;
use StockAvailable;

/**
 * Increase or decrease quantity of an order's product.
 * Recalculate cart rules, order's prices and shipping infos.
 */
class OrderProductQuantityUpdater
{
    /**
     * @var OrderAmountUpdater
     */
    private $orderAmountUpdater;

    /**
     * @var ContextStateManager
     */
    private $contextStateManager;

    /**
     * @var OrderProductRemover
     */
    private $orderProductRemover;

    public function __construct(
        OrderAmountUpdater $orderAmountUpdater,
        OrderProductRemover $orderProductRemover,
        ContextStateManager $contextStateManager
    ) {
        $this->orderAmountUpdater = $orderAmountUpdater;
        $this->orderProductRemover = $orderProductRemover;
        $this->contextStateManager = $contextStateManager;
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param int $newQuantity
     * @param OrderInvoice|null $orderInvoice
     * @param bool $updateCart Used when you don't want to update the cart (CartRule removal for example)
     *
     * @return Order
     *
     * @throws OrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function update(
        Order $order,
        OrderDetail $orderDetail,
        int $newQuantity,
        ?OrderInvoice $orderInvoice,
        bool $updateCart = true
    ): Order {
        $cart = new Cart($order->id_cart);

        $this->contextStateManager
            ->saveCurrentContext()
            ->setCart($cart)
            ->setCurrency(new Currency($cart->id_currency))
            ->setCustomer(new Customer($cart->id_customer))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setCountry($cart->getTaxCountry())
            ->setShop(new Shop($cart->id_shop))
        ;

        try {
            $this->updateOrderDetail($order, $cart, $orderDetail, $newQuantity, $orderInvoice, $updateCart);

            // Update prices on the order after cart rules are recomputed
            $this->orderAmountUpdater->update($order, $cart, null !== $orderInvoice ? (int) $orderInvoice->id : null);
        } finally {
            $this->contextStateManager->restorePreviousContext();
        }

        return $order;
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $newQuantity
     * @param OrderInvoice|null $orderInvoice
     * @param bool $updateCart
     *
     * @throws OrderException
     * @throws ProductOutOfStockException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderDetail(
        Order $order,
        Cart $cart,
        OrderDetail $orderDetail,
        int $newQuantity,
        ?OrderInvoice $orderInvoice,
        bool $updateCart
    ): void {
        $oldQuantity = (int) $orderDetail->product_quantity;

        // Perform deletion first, we don't want the OrderDetail to be saved with a quantity 0, this could lead to bugs
        if (0 === $newQuantity) {
            // Product deletion
            $cartComparator = $this->orderProductRemover->deleteProductFromOrder($order, $orderDetail, $updateCart);
            if ((int) $orderDetail->id_customization) {
                $this->deleteProductCustomization((int) $orderDetail->id_customization);
            }
            $this->applyOtherProductUpdates($order, $cart, $orderInvoice, $cartComparator->getUpdatedProducts());
            $this->applyOtherProductCreation($order, $cart, $orderInvoice, $cartComparator->getAdditionalProducts());
        } else {
            $this->assertValidProductQuantity($orderDetail, $newQuantity);
            // It's important to override the invoice, this is what allows to switch an OrderDetail from an invoice to another
            if (null !== $orderInvoice) {
                $orderDetail->id_order_invoice = $orderInvoice->id;
            }

            $orderDetail->product_quantity = $newQuantity;
            $orderDetail->reduction_percent = 0;
            $orderDetail->update();

            // Update quantity on the cart and stock
            if ($updateCart) {
                $cartComparator = $this->updateProductQuantity($cart, $orderDetail, $oldQuantity, $newQuantity);
                $this->applyOtherProductUpdates($order, $cart, $orderInvoice, $cartComparator->getUpdatedProducts());
                $this->applyOtherProductCreation($order, $cart, $orderInvoice, $cartComparator->getAdditionalProducts());
            }
        }

        // Update product stocks
        $this->updateStocks($cart, $orderDetail, $oldQuantity, $newQuantity);
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param OrderInvoice|null $orderInvoice
     * @param CartProductUpdate[] $updatedProducts
     */
    private function applyOtherProductUpdates(
        Order $order,
        Cart $cart,
        ?OrderInvoice $orderInvoice,
        array $updatedProducts
    ): void {
        // Some products have been affected by the removal of the initial product (probably related to a CartRule)
        // So we detect the changes that happened in the cart and apply them on the OrderDetail
        $orderDetails = $order->getOrderDetailList();
        foreach ($updatedProducts as $updatedProduct) {
            $updatedCombinationId = $updatedProduct->getCombinationId() !== null
                ? $updatedProduct->getCombinationId()->getValue()
                : 0;
            $updatedOrderDetail = null;
            foreach ($orderDetails as $orderDetailData) {
                if ((int) $orderDetailData['product_id'] === $updatedProduct->getProductId()->getValue()
                    && (int) $orderDetailData['product_attribute_id'] === $updatedCombinationId) {
                    $updatedOrderDetail = new OrderDetail($orderDetailData['id_order_detail']);
                    break;
                }
            }

            if (null !== $updatedOrderDetail) {
                $newUpdatedQuantity = (int) $updatedOrderDetail->product_quantity + $updatedProduct->getDeltaQuantity();
                // Important: we update the OrderDetail but not the cart (it is already updated) to avoid infinite loop
                $this->updateOrderDetail(
                    $order,
                    $cart,
                    $updatedOrderDetail,
                    $newUpdatedQuantity,
                    $orderInvoice,
                    false
                );
            }
        }
    }

    /**
     * @param Order $order
     * @param Cart $cart
     * @param OrderInvoice|null $orderInvoice
     * @param array $createdProducts
     */
    private function applyOtherProductCreation(
        Order $order,
        Cart $cart,
        ?OrderInvoice $orderInvoice,
        array $createdProducts
    ): void {
        $productsToAdd = [];
        foreach ($createdProducts as $createdProduct) {
            $updatedCombinationId = $createdProduct->getCombinationId() !== null
                ? $createdProduct->getCombinationId()->getValue()
                : 0;
            foreach ($cart->getProducts() as $product) {
                if ((int) $product['id_product'] === $createdProduct->getProductId()->getValue()
                    && (int) $product['id_product_attribute'] === $updatedCombinationId) {
                    $productsToAdd[] = $product;
                    break;
                }
            }
        }
        if (count($productsToAdd) > 0) {
            $orderDetail = new OrderDetail();
            $orderDetail->createList(
                $order,
                $cart,
                $order->getCurrentState(),
                $productsToAdd,
                $orderInvoice ? $orderInvoice->id : 0
            );
        }
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @return CartProductsComparator
     */
    private function updateProductQuantity(
        Cart $cart,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity
    ): CartProductsComparator {
        $cartComparator = new CartProductsComparator($cart);

        $deltaQuantity = $newQuantity - $oldQuantity;
        if (0 === $deltaQuantity) {
            return $cartComparator;
        }

        $knownUpdates = [
            new CartProductUpdate(
                (int) $orderDetail->product_id,
                (int) $orderDetail->product_attribute_id,
                $deltaQuantity,
                false,
                (int) $orderDetail->id_customization
            ),
        ];
        $cartComparator->setKnownUpdates($knownUpdates);

        /**
         * Here we update product and customization in the cart.
         *
         * The last argument "skip quantity check" is set to true because
         * 1) the quantity has already been checked,
         * 2) (main reason) when the cart checks the availability ; it substracts
         * its own quantity from available stock.
         *
         * This is because a product in a cart is not really out of the stock, because it is not checked out yet.
         *
         * Here we are editing an order, not a cart, so what has been ordered
         * has already been substracted from the stock.
         */
        $updateQuantityResult = $cart->updateQty(
            abs($deltaQuantity),
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            $orderDetail->id_customization,
            $deltaQuantity < 0 ? 'down' : 'up',
            0,
            new Shop($cart->id_shop),
            true,
            true
        );

        if (-1 === $updateQuantityResult) {
            throw new \LogicException('Minimum quantity is not respected');
        } elseif (true !== $updateQuantityResult) {
            throw new \LogicException('Something went wrong');
        }

        return $cartComparator;
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @throws OrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateStocks(Cart $cart, OrderDetail $orderDetail, int $oldQuantity, int $newQuantity): void
    {
        $deltaQuantity = $oldQuantity - $newQuantity;

        if (0 === $deltaQuantity) {
            return;
        }

        if (0 === $newQuantity) {
            // Product deletion. Reinject quantity in stock
            $this->reinjectQuantity($orderDetail, $oldQuantity, $newQuantity, true);
        } elseif ($deltaQuantity > 0) {
            // Increase product quantity
            StockAvailable::updateQuantity(
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $deltaQuantity,
                $cart->id_shop,
                true,
                [
                    'id_order' => $orderDetail->id_order,
                    'id_stock_mvt_reason' => Configuration::get('PS_STOCK_CUSTOMER_RETURN_REASON'),
                ]
            );
        } else {
            // Decrease product quantity. Reinject quantity in stock
            $this->reinjectQuantity($orderDetail, $oldQuantity, $newQuantity, false);
        }
    }

    /**
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     * @param bool $delete
     *
     * @throws OrderException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function reinjectQuantity(
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity,
        $delete = false
    ) {
        // Reinject product
        $reinjectableQuantity = $oldQuantity - $newQuantity;
        $quantityToReinject = $oldQuantity > $reinjectableQuantity ? $reinjectableQuantity : $oldQuantity;

        StockAvailable::updateQuantity(
            $orderDetail->product_id,
            $orderDetail->product_attribute_id,
            $quantityToReinject,
            $orderDetail->id_shop,
            true,
            [
                'id_order' => $orderDetail->id_order,
                'id_stock_mvt_reason' => Configuration::get('PS_STOCK_CUSTOMER_RETURN_REASON'),
            ]
        );

        // sync all stock
        (new StockManager())->updatePhysicalProductQuantity(
            (int) $orderDetail->id_shop,
            (int) Configuration::get('PS_OS_ERROR'),
            (int) Configuration::get('PS_OS_CANCELED'),
            null,
            (int) $orderDetail->id_order
        );

        if ($delete) {
            $orderDetail->delete();
        }
    }

    /**
     * @param int $id_customization
     *
     * @throws OrderException
     */
    private function deleteProductCustomization(int $id_customization): void
    {
        if (!Db::getInstance()->execute(
            'DELETE FROM `' . _DB_PREFIX_ . 'customization` 
            WHERE `id_customization` = ' . (int) $id_customization)) {
            throw new OrderException('Could not delete customization from database.');
        }
    }

    /**
     * @param OrderDetail $orderDetail
     * @param int $newQuantity
     *
     * @throws ProductOutOfStockException
     */
    private function assertValidProductQuantity(OrderDetail $orderDetail, int $newQuantity)
    {
        //check if product is available in stock
        if (!Product::isAvailableWhenOutOfStock(StockAvailable::outOfStock($orderDetail->product_id))) {
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct(
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $orderDetail->id_shop
            );
            $quantityDiff = $newQuantity - (int) $orderDetail->product_quantity;

            if ($quantityDiff > $availableQuantity) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }
}
