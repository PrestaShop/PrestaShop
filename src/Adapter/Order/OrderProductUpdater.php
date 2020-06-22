<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Address;
use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use Language;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
use OrderInvoice;
use Pack;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderProductRemover;
use PrestaShop\PrestaShop\Adapter\StockManager;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ComputingPrecision;
use Product;
use StockAvailable;
use StockManagerFactory;
use StockMvt;
use Tools;
use Validate;
use Warehouse;

/**
 * Increase or decrease quantity of an order's product.
 * Recalculate cart rules, order's prices and shipping infos.
 */
class OrderProductUpdater
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
     * @param int $oldQuantity
     * @param int $newQuantity
     * @param bool|null $productDeletionMode
     * @param bool|null $hasInvoice
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
        int $oldQuantity,
        int $newQuantity,
        ?OrderInvoice $orderInvoice
    ): Order {
        $cart = new Cart($order->id_cart);

        $this->contextStateManager
            ->setCart($cart)
            ->setCurrency(new Currency($cart->id_currency))
            ->setCustomer(new Customer($cart->id_customer))
            ->setLanguage(new Language($cart->id_lang))
            ->setCountry($this->getTaxCountry($cart))
        ;

        try {
            // Update quantity on the cart and stock
            $cart = $this->updateProductQuantity($cart, $order, $orderDetail, $oldQuantity, $newQuantity);

            // Update product stocks
            $this->updateStocks($cart, $orderDetail, $oldQuantity, $newQuantity);

            // Recalculate cart rules and Fix differences between cart's cartRules and order's cartRules
            $this->updateOrderCartRules($order, $cart);

            // Update prices on the order
            $order = $this->updateOrderAmounts($cart, $order, ($orderInvoice && !empty($orderInvoice->id)));

            // Update weight and shipping infos
            $order = $this->updateOrderShippingInfos($order, new Product((int) $orderDetail->product_id));

            $this->updateOrderInvoice($orderDetail, $orderInvoice);
        } finally {
            $this->contextStateManager->restoreContext();
        }

        return $order;
    }

    /**
     * @param Cart $cart
     * @param OrderDetail $orderDetail
     * @param int $oldQuantity
     * @param int $newQuantity
     *
     * @return Cart
     */
    private function updateProductQuantity(
        Cart $cart,
        Order $order,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity
    ): Cart {
        $deltaQuantity = $oldQuantity - $newQuantity;

        if (0 === $deltaQuantity) {
            return $cart;
        }

        if (0 === $newQuantity) {
            // Product deletion
            $this->orderProductRemover->deleteProductFromOrder($order, $orderDetail, $oldQuantity);

            $this->updateCustomizationOnProductDelete($order, $orderDetail, $oldQuantity);
        } else {
            // Update product and customization in the cart
            $updateQuantityResult = $cart->updateQty(
                abs($deltaQuantity),
                $orderDetail->product_id,
                $orderDetail->product_attribute_id,
                false,
                $deltaQuantity > 0 ? 'down' : 'up'
            );

            if (-1 === $updateQuantityResult) {
                throw new \LogicException('Minimum quantity is not respected');
            } elseif (true !== $updateQuantityResult) {
                throw new \LogicException('Something went wrong');
            }
        }

        return $cart;
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
                $cart->id_shop
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
     * Remove previous cart rules applied to order
     *
     * @param Order $order
     * @param Cart $cart
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderCartRules(Order $order, Cart $cart): void
    {
        Context::getContext()->cart = $cart;
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        $computingPrecision = $this->getPrecisionFromCart($cart);
        $newCartRules = $cart->getCartRules();
        foreach ($order->getCartRules() as $orderCartRuleData) {
            foreach ($newCartRules as $newCartRule) {
                if ($newCartRule['id_cart_rule'] == $orderCartRuleData['id_cart_rule']) {
                    // Cart rule is still in the cart no need to remove it, but we update it as the amount may have changed
                    $cartRule = new CartRule($newCartRule['id_cart_rule']);

                    $orderCartRule = new OrderCartRule($orderCartRuleData['id_order_cart_rule']);
                    $orderCartRule->id_order = $order->id;
                    $orderCartRule->name = $newCartRule['name'];
                    $orderCartRule->value = Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision);
                    $orderCartRule->value_tax_excl = Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision);
                    $orderCartRule->save();
                    continue 2;
                }
            }

            // This one is no longer in the new cart rules so we delete it
            $orderCartRule = new OrderCartRule($orderCartRuleData['id_order_cart_rule']);
            if (!$orderCartRule->delete()) {
                throw new OrderException('Could not delete order cart rule from database.');
            }
        }

        // Finally add the new cart rules that are not in the Order
        foreach ($newCartRules as $newCartRule) {
            foreach ($order->getCartRules() as $orderCartRuleData) {
                if ($newCartRule['id_cart_rule'] == $orderCartRuleData['id_cart_rule']) {
                    // This cart rule is already present no need to add it
                    continue 2;
                }
            }

            // Add missing order cart rule
            $cartRule = new CartRule($newCartRule['id_cart_rule']);

            $orderCartRule = new OrderCartRule();
            $orderCartRule->id_order = $order->id;
            $orderCartRule->id_cart_rule = $newCartRule['id_cart_rule'];
            $orderCartRule->id_order_invoice = $order->getInvoicesCollection()->getLast();
            $orderCartRule->name = $newCartRule['name'];
            $orderCartRule->value = Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision);
            $orderCartRule->value_tax_excl = Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision);
            $orderCartRule->save();
        }
    }

    /**
     * @param Cart $cart
     * @param Order $order
     * @param bool $hasInvoice
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderAmounts(Cart $cart, Order $order, bool $hasInvoice): Order
    {
        $order = $this->orderAmountUpdater->update($order, $cart, $hasInvoice);
        $order->update();

        return $order;
    }

    /**
     * @param Order $order
     * @param Product $product
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderShippingInfos(Order $order, Product $product): Order
    {
        $orderCarrier = new OrderCarrier((int) $order->getIdOrderCarrier());

        if (Validate::isLoadedObject($orderCarrier)) {
            $orderCarrier->weight = (float) $order->getTotalWeight();

            if ($orderCarrier->update()) {
                $order->weight = sprintf('%.3f ' . Configuration::get('PS_WEIGHT_UNIT'), $orderCarrier->weight);
            }
        }

        if (!$product->is_virtual) {
            $order = $order->refreshShippingCost();
        }

        return $order;
    }

    /**
     * @param OrderDetail $orderDetail
     * @param OrderInvoice|null $orderInvoice
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function updateOrderInvoice(OrderDetail $orderDetail, ?OrderInvoice $orderInvoice): void
    {
        if ($orderDetail->id_order_invoice != 0) {
            $orderDetailInvoice = new OrderInvoice($orderDetail->id_order_invoice);
            // @todo: use https://github.com/PrestaShop/decimal for price computations
            $orderDetailInvoice->total_paid_tax_excl -= $orderDetail->total_price_tax_excl;
            $orderDetailInvoice->total_paid_tax_incl -= $orderDetail->total_price_tax_incl;
            $orderDetailInvoice->total_products -= $orderDetail->total_price_tax_excl;
            $orderDetailInvoice->total_products_wt -= $orderDetail->total_price_tax_incl;

            $orderDetailInvoice->update();
        }

        // Apply change on OrderInvoice
        if (isset($orderInvoice) && $orderDetail->id_order_invoice != $orderInvoice->id) {
            $orderInvoice->total_products += $orderDetail->total_price_tax_excl;
            $orderInvoice->total_products_wt += $orderDetail->total_price_tax_incl;

            $orderInvoice->total_paid_tax_excl += $orderDetail->total_price_tax_excl;
            $orderInvoice->total_paid_tax_incl += $orderDetail->total_price_tax_incl;

            $orderDetail->id_order_invoice = $orderInvoice->id;

            $orderInvoice->update();
            $orderDetail->update();
        }
    }

    /**
     * @param Cart $cart
     *
     * @return int
     */
    private function getPrecisionFromCart(Cart $cart): int
    {
        $computingPrecision = new ComputingPrecision();
        $currency = new Currency((int) $cart->id_currency);

        return $computingPrecision->getPrecision((int) $currency->precision);
    }

    /**
     * @param Cart $cart
     *
     * @return Country
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function getTaxCountry(Cart $cart): Country
    {
        $taxAddressType = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $taxAddressId = property_exists($cart, $taxAddressType) ? $cart->{$taxAddressType} : $cart->id_address_delivery;
        $taxAddress = new Address($taxAddressId);

        return new Country($taxAddress->id_country);
    }
}
