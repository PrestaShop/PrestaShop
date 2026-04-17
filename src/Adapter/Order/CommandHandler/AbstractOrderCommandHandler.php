<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
use PrestaShopDatabaseException;
use PrestaShopException;
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
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function getCartTaxCountry(Cart $cart): Country
    {
        $taxAddressType = Configuration::get('PS_TAX_ADDRESS_TYPE');
        $taxAddressId = property_exists($cart, $taxAddressType) ? $cart->{$taxAddressType} : $cart->id_address_delivery;
        $taxAddress = new Address($taxAddressId);

        return new Country($taxAddress->id_country);
    }
}
