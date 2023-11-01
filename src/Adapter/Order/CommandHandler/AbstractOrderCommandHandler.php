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
use Configuration;
use Country;
use Currency;
use Customer;
use Order;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Order\AbstractOrderHandler;
use PrestaShop\PrestaShop\Adapter\StockManager;
use Product;
use Shop;
use StockAvailable;

/**
 * Abstracts reusable functionality for Order subdomain handlers.
 *
 * @internal
 */
abstract class AbstractOrderCommandHandler extends AbstractOrderHandler
{
    /**
     * @param OrderDetail $orderDetail
     * @param int $productQuantity
     * @param bool $delete
     */
    protected function reinjectQuantity(OrderDetail $orderDetail, $productQuantity, $delete = false)
    {
        // Reinject product
        $reinjectableQuantity = (int) $orderDetail->product_quantity - (int) $orderDetail->product_quantity_reinjected;
        $quantityToReinject = $productQuantity > $reinjectableQuantity ? $reinjectableQuantity : $productQuantity;

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
     * @param ContextStateManager $contextStateManager
     * @param Cart $cart
     */
    protected function setCartContext(ContextStateManager $contextStateManager, Cart $cart): void
    {
        $contextStateManager
            ->saveCurrentContext()
            ->setCart($cart)
            ->setCustomer(new Customer($cart->id_customer))
            ->setCurrency(new Currency($cart->id_currency))
            ->setLanguage($cart->getAssociatedLanguage())
            ->setCountry($this->getCartTaxCountry($cart))
            ->setShop(new Shop($cart->id_shop))
        ;
    }

    /**
     * @param ContextStateManager $contextStateManager
     * @param Order $order
     */
    protected function setOrderContext(ContextStateManager $contextStateManager, Order $order): void
    {
        $cart = new Cart($order->id_cart);
        $this->setCartContext($contextStateManager, $cart);
    }

    /**
     * @param Cart $cart
     *
     * @return Country
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getCartTaxCountry(Cart $cart): Country
    {
        $taxAddressType = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $taxAddressId = property_exists($cart, $taxAddressType) ? $cart->{$taxAddressType} : $cart->id_address_delivery;
        $taxAddress = new Address($taxAddressId);

        return new Country($taxAddress->id_country);
    }
}
