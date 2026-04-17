<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Cart;

/*
 * Cart status
 */
enum CartStatus
{
    /**
     * Cart ordered
     */
    public const ORDERED = 'ordered';

    /**
     * Cart not ordered
     */
    public const NOT_ORDERED = 'not_ordered';

    /**
     * Cart not ordered but for long time
     */
    public const ABANDONED_CART = 'abandoned_cart';

    /**
     * Time in seconds representing time before not ordered carts are considered abandoned
     * (For now: 24h)
     */
    public const ABANDONED_CART_EXPIRATION_TIME = 86400;
}
