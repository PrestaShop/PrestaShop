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
use Language;
use Order;
use OrderCarrier;
use OrderCartRule;
use OrderDetail;
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
     * @param bool $hasInvoice
     *
     * @return Order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function update(
        Order $order,
        OrderDetail $orderDetail,
        int $oldQuantity,
        int $newQuantity,
        bool $hasInvoice = false
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

            // Fix differences between cart's cartRules and order's cartRules
            $cart = $this->syncCartAndOrderCartRules($cart, $order, $newQuantity <= 0);

            // Recalculate amounts of cartRules
            $this->recalculateCartRules($cart, $order);

            // Update prices on the order
            $order = $this->updateOrderAmounts($cart, $order, $hasInvoice);

            // Update weight and shipping infos
            $order = $this->updateOrderShippingInfos($order, new Product((int) $orderDetail->product_id));
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
        } else {
            // Update product in the cart
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
     * @param Cart $cart
     * @param Order $order
     * @param bool $delete
     *
     * @return Cart
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function syncCartAndOrderCartRules(Cart $cart, Order $order, bool $delete = false): Cart
    {
        Context::getContext()->cart = $cart;
        CartRule::autoAddToCart();
        CartRule::autoRemoveFromCart();

        $orderCartRulesData = $order->getCartRules();
        $orderCartRulesIds = array_map(
            function (array $orderCartRuleData) { return (int) $orderCartRuleData['id_cart_rule']; },
            $orderCartRulesData
        );

        $cartRules = $cart->getCartRules();
        $cartRulesIds = array_map(
            function (array $cartRule) { return (int) $cartRule['id_cart_rule']; },
            $cartRules
        );

        if (count($orderCartRulesIds) === count($cartRulesIds)) {
            return $cart;
        }

        if (count($orderCartRulesIds) > count($cartRulesIds)) {
            if ($delete) { // Product deletion, the extra order cart rules have to be deleted
                foreach ($order->getCartRules() as $orderCartRule) {
                    foreach (array_diff($orderCartRulesIds, $cartRulesIds) as $idCartRule) {
                        if ($idCartRule == $orderCartRule['id_cart_rule']) {
                            $orderCartRule = new OrderCartRule($orderCartRule['id_order_cart_rule']);
                            $orderCartRule->delete();
                        }
                    }
                }
            } else { // desynchronization of cartRules, need to be fixed
                sort($orderCartRulesIds);
                sort($cartRulesIds);

                foreach (array_diff($orderCartRulesIds, $cartRulesIds) as $cartRuleId) {
                    $cart->addCartRule($cartRuleId);
                }
            }
        } else {
            throw new \LogicException('Cart has more cart rules than order. This should never happen.');
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @param Order $order
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function recalculateCartRules(Cart $cart, Order $order): void
    {
        $computingPrecision = $this->getPrecisionFromCart($cart);

        foreach ($order->getCartRules() as $orderCartRuleData) {
            $orderCartRule = new OrderCartRule((int) $orderCartRuleData['id_order_cart_rule']);
            $idCartRule = (int) $orderCartRule->id_cart_rule;
            $cartRule = new CartRule($idCartRule);
            $values = [
                'tax_incl' => Tools::ps_round($cartRule->getContextualValue(true), $computingPrecision),
                'tax_excl' => Tools::ps_round($cartRule->getContextualValue(false), $computingPrecision),
            ];

            if (
                ($values['tax_incl'] !== Tools::ps_round($orderCartRule->value, $computingPrecision)) ||
                ($values['tax_excl'] !== Tools::ps_round($orderCartRule->value_tax_excl, $computingPrecision))
            ) {
                $orderCartRule->value = $values['tax_incl'];
                $orderCartRule->value_tax_excl = $values['tax_excl'];

                $orderCartRule->update();
            }
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
