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

namespace PrestaShop\PrestaShop\Adapter\Cart;

use Cart;
use Currency;
use Customer;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShopException;
use Shop;

/**
 * Provides reusable methods for cart handlers
 *
 * @internal
 */
abstract class AbstractCartHandler
{
    /**
     * @param CartId $cartId
     *
     * @return Cart
     *
     * @throws CartNotFoundException
     */
    protected function getCart(CartId $cartId)
    {
        try {
            $cart = new Cart($cartId->getValue());
        } catch (PrestaShopException $e) {
            throw new CartException(sprintf('An error occurred when trying to load cart with id "%s', $cartId->getValue()));
        }

        if (!Validate::isLoadedObject($cart) || $cartId->getValue() !== (int) $cart->id) {
            throw new CartNotFoundException(sprintf('Cart with id "%s" was not found', $cartId->getValue()));
        }

        return $cart;
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
            ->setCountry($cart->getTaxCountry())
            ->setShop(new Shop($cart->id_shop))
        ;
    }
}
