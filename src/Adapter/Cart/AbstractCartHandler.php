<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Cart;

use Cart;
use Context;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

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
    protected function getContextCartObject(CartId $cartId)
    {
        // Legacy behavior is working with context cart instead of retrieving cart from db
        $cart = Context::getContext()->cart;

        if (!Validate::isLoadedObject($cart) || $cartId->getValue() !== (int) $cart->id) {
            throw new CartNotFoundException(
                sprintf('Cart with id "%s" was not found', $cartId->getValue())
            );
        }

        return $cart;
    }
}
