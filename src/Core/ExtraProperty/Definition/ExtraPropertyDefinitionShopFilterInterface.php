<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Filters a definition collection down to the definitions available for a shop scope.
 *
 * This is the impure counterpart of ExtraPropertyDefinitionCollection::filterByShops():
 * the collection is a pure value object, but availability depends on two pieces of state
 * only a service can resolve — the shop ids covered by a ShopConstraint (group and
 * all-shops shapes need a DB lookup) and, for module-owned definitions without an
 * explicit restriction, the owning module's enabled shops (ps_module_shop).
 *
 * Availability rules are documented on ExtraPropertyDefinition::isAvailableForShops().
 */
interface ExtraPropertyDefinitionShopFilterInterface
{
    /**
     * Returns the definitions available for at least one shop covered by the constraint.
     *
     * When the multistore feature is disabled the collection is returned unfiltered:
     * by convention every definition belongs to the only shop, and stale association
     * rows left over from a former multistore setup are ignored.
     */
    public function filterByShopConstraint(
        ExtraPropertyDefinitionCollection $definitions,
        ShopConstraint $shopConstraint,
    ): ExtraPropertyDefinitionCollection;

    /**
     * Same as filterByShopConstraint() for an already-resolved list of shop ids.
     *
     * @param list<int> $shopIds
     */
    public function filterByShopIds(
        ExtraPropertyDefinitionCollection $definitions,
        array $shopIds,
    ): ExtraPropertyDefinitionCollection;

    /**
     * Returns the subset of $shopIds one definition is available for — the per-definition
     * counterpart of filterByShopIds(), used by the writer to intersect its fan-out list
     * per definition (an empty result means "not available anywhere in this scope").
     *
     * When the multistore feature is disabled, $shopIds is returned unchanged.
     *
     * @param list<int> $shopIds
     *
     * @return list<int>
     */
    public function getAvailableShopIds(ExtraPropertyDefinition $definition, array $shopIds): array;
}
