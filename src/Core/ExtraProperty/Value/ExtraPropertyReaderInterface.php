<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;

/**
 * Reads extra property values for a given entity instance.
 *
 * Used by ObjectModel (via ServiceLocator) and front-office LazyArray / presenter contexts.
 * Values are grouped by module technical name then by field name.
 */
interface ExtraPropertyReaderInterface
{
    /**
     * Returns extra property values for one entity instance, grouped by module name.
     *
     * Format:
     * [
     *     'module_technical_name' => [
     *         'property_name' => 'value_or_lang_array',
     *     ],
     * ]
     *
     * Values are TYPED: each value is cast from its raw DB string to the declared PHP type
     * (ExtraPropertyValueCaster::castFromDb — bool/int/float, 'Y-m-d H:i:s' date strings).
     * NULLs are nullable-aware: kept for nullable columns, BOOL coerced to false otherwise.
     *
     * Lang-scope fields:
     *   - $langId given  → one scalar value per field
     *   - $langId null   → array keyed by id_lang: ['property' => [1 => 'en', 2 => 'fr']]
     *
     * Per-shop values (SHOP scope, and LANG scope on multilang-multishop entities) always
     * return a single scalar for the given ShopConstraint: a single-shop constraint reads
     * that shop's row; a shop group / all shops / collection constraint is resolved to its
     * deterministic representative shop (the default shop when it belongs to the scope,
     * the lowest shop id of the scope otherwise). Whether the lang table is shop-aware is
     * detected internally from the storage schema — never passed by the caller.
     *
     * @param string $entityName PHYSICAL entity table name without prefix (ObjectModel $definition['table'] / ExtraPropertyDefinition::getTableName(), e.g. "product", "product_attribute") — never the logical entity name, which differs for irregular entities
     * @param string $primaryKeyName PK column name (e.g. "id_product")
     * @param int $entityId
     * @param int|null $langId Null fetches all languages (returns array keyed by id_lang)
     * @param ShopConstraint $shopConstraint Shop context — determines which row to read
     * @param ExtraPropertyDefinitionCollection|null $definitions Pre-filtered definitions; when null, all definitions for $entityName are loaded from the repository
     *
     * @return array<string, array<string, mixed>>
     */
    public function getExtraProperties(
        string $entityName,
        string $primaryKeyName,
        int $entityId,
        ?int $langId,
        ShopConstraint $shopConstraint,
        ?ExtraPropertyDefinitionCollection $definitions = null,
    ): array;

    /**
     * Same as {@see getExtraProperties()} but for several entity instances at once, fetched with a single query
     * per scope (not a per-id loop). Useful to enrich a whole list/collection response without N+1 reads.
     *
     * @param int[] $entityIds Entity ids to read (non-positive ids and duplicates are ignored)
     *
     * @return array<int, array<string, array<string, mixed>>> Per entity id: the same grouped structure
     *                                                         getExtraProperties() returns. Ids with no row still
     *                                                         appear, seeded with each property's default value.
     */
    public function getMultipleExtraProperties(
        string $entityName,
        string $primaryKeyName,
        array $entityIds,
        ?int $langId,
        ShopConstraint $shopConstraint,
        ?ExtraPropertyDefinitionCollection $definitions = null,
    ): array;
}
