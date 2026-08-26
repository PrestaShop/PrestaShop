<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Shop;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Resolves the concrete shops covered by a ShopConstraint.
 *
 * A ShopConstraint can express a single shop, a shop group, an explicit shop list
 * (ShopCollection) or all shops. Code that must act per shop (fan-out writes) or pin
 * a deterministic single shop (reads, grid joins, context shop id) resolves the
 * constraint through this service instead of re-implementing the mapping.
 */
interface ShopListResolverInterface
{
    /**
     * Returns every shop id covered by the constraint:
     * single shop → [id]; ShopCollection → its ids; shop group → the group's shops; all shops → every shop.
     *
     * @return list<int>
     */
    public function resolveShopIds(ShopConstraint $shopConstraint): array;

    /**
     * Returns the single deterministic shop representing the constraint's scope:
     * the configured default shop (PS_SHOP_DEFAULT) when it belongs to the scope,
     * the lowest shop id of the scope otherwise. Returns 0 when the scope is empty
     * (e.g. a group without shops).
     */
    public function resolveRepresentativeShopId(ShopConstraint $shopConstraint): int;
}
