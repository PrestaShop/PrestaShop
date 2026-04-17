<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Cart\Provider;

/**
 * Provides the list of products in a cart. Different implementations for
 * real database access (FO) and unit testing.
 *
 * @experimental
 */
interface CartProductProviderInterface
{
    /**
     * @return CartProduct[]
     */
    public function getCartProducts(int $cartId): array;
}
