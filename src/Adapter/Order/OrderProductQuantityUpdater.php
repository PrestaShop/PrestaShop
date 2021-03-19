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
use Context;
use Currency;
use Customer;
use Customization;
use Db;
use Order;
use OrderDetail;
use OrderInvoice;
use Pack;
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
use StockManagerFactory;
use StockMvt;
use Warehouse;

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
            $updatedProducts = $this->orderProductRemover->deleteProductFromOrder($order, $orderDetail, $updateCart);
            $this->updateCustomizationOnProductDelete($order, $orderDetail, $oldQuantity);
            $this->applyOtherProductUpdates($order, $cart, $orderInvoice, $updatedProducts);
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
                $updatedProducts = $this->updateProductQuantity($cart, $orderDetail, $oldQuantity, $newQuantity);
                $this->applyOtherProductUpdates($order, $cart, $orderInvoice, $updatedProducts);
            } elseif ($orderDetail->id_customization > 0) {
                $customization = new Customization($orderDetail->id_customization);
                $customization->quantity = $newQuantity;
                $customization->save();
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
    ) {
        // Some products have been affected by the removal of the initial product (probably related to a CartRule)
        // So we detect the changes that happened in the cart and apply them on the OrderDetail
        $orderDetails = $order->getOrderDetailList();
        foreach ($updatedProducts as $updatedProduct) {
            $updatedCombinationId = $updatedProduct->getCombinationId() !== null ? $updatedProduct->getCombinationId()->getValue() : 0;
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
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @return array
     */
    private function updateProductQuantity(
        Cart $cart,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity
    ): array {
        $deltaQuantity = $newQuantity - $oldQuantity;

        if (0 === $deltaQuantity) {
            return [];
        }

        $cartComparator = new CartProductsComparator($cart);
        $knownUpdates = [
            new CartProductUpdate(
                (int) $orderDetail->product_id,
                (int) $orderDetail->product_attribute_id,
                $deltaQuantity,
                false
            ),
        ];

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

        return $cartComparator->getUpdatedProducts($knownUpdates);
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

        $product = new Product(
            $orderDetail->product_id,
            false,
            (int) Context::getContext()->language->id,
            (int) $orderDetail->id_shop
        );

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
            && $product->advanced_stock_management
            && $orderDetail->id_warehouse != 0
        ) {
            $manager = StockManagerFactory::getManager();
            $movements = StockMvt::getNegativeStockMvts(
                $orderDetail->id_order,
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                $quantityToReinject
            );

            foreach ($movements as $movement) {
                if ($quantityToReinject > $movement['physical_quantity']) {
                    $quantityToReinject = $movement['physical_quantity'];
                }

                if (Pack::isPack((int) $product->id)) {
                    // Gets items
                    if ($product->pack_stock_type == Pack::STOCK_TYPE_PRODUCTS_ONLY
                        || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                        || ($product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                            && Configuration::get('PS_PACK_STOCK_TYPE') > 0)
                    ) {
                        $products_pack = Pack::getItems((int) $product->id, (int) Configuration::get('PS_LANG_DEFAULT'));
                        // Foreach item
                        foreach ($products_pack as $product_pack) {
                            if ($product_pack->advanced_stock_management == 1) {
                                $manager->addProduct(
                                    $product_pack->id,
                                    $product_pack->id_pack_product_attribute,
                                    new Warehouse($movement['id_warehouse']),
                                    $product_pack->pack_quantity * $quantityToReinject,
                                    null,
                                    $movement['price_te']
                                );
                            }
                        }
                    }

                    if ($product->pack_stock_type == Pack::STOCK_TYPE_PACK_ONLY
                        || $product->pack_stock_type == Pack::STOCK_TYPE_PACK_BOTH
                        || (
                            $product->pack_stock_type == Pack::STOCK_TYPE_DEFAULT
                            && (Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_ONLY
                                || Configuration::get('PS_PACK_STOCK_TYPE') == Pack::STOCK_TYPE_PACK_BOTH)
                        )
                    ) {
                        $manager->addProduct(
                            $orderDetail->product_id,
                            $orderDetail->product_attribute_id,
                            new Warehouse($movement['id_warehouse']),
                            $quantityToReinject,
                            null,
                            $movement['price_te']
                        );
                    }
                } else {
                    $manager->addProduct(
                        $orderDetail->product_id,
                        $orderDetail->product_attribute_id,
                        new Warehouse($movement['id_warehouse']),
                        $quantityToReinject,
                        null,
                        $movement['price_te']
                    );
                }
            }

            $productId = $orderDetail->product_id;

            if ($delete) {
                $orderDetail->delete();
            }

            StockAvailable::synchronize($productId);
        } elseif ($orderDetail->id_warehouse == 0) {
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
        } else {
            throw new OrderException('This product cannot be re-stocked.');
        }
    }

    /**
     * @param Order $order
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     *
     * @throws OrderException
     */
    private function updateCustomizationOnProductDelete(Order $order, OrderDetail $orderDetail, int $oldQuantity): void
    {
        if (!(int) $order->getCurrentState()) {
            throw new OrderException('Could not get a valid Order state before deletion');
        }

        if ($order->hasBeenPaid()) {
            Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'customization` SET `quantity_refunded` = `quantity_refunded` + ' . (int) $oldQuantity . ' WHERE `id_customization` = ' . (int) $orderDetail->id_customization . ' AND `id_cart` = ' . (int) $order->id_cart . ' AND `id_product` = ' . (int) $orderDetail->product_id);
        }

        if (!Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'customization` WHERE `quantity` = 0')) {
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
            $availableQuantity = StockAvailable::getQuantityAvailableByProduct($orderDetail->product_id, $orderDetail->product_attribute_id);
            $quantityDiff = $newQuantity - (int) $orderDetail->product_quantity;

            if ($quantityDiff > $availableQuantity) {
                throw new ProductOutOfStockException('Not enough products in stock');
            }
        }
    }
}
