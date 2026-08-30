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
     * Shop-scope fields always return a single scalar for the given ShopConstraint.
     *
     * @param string $entityName Entity table name (e.g. "product")
     * @param string $primaryKeyName PK column name (e.g. "id_product")
     * @param int $entityId
     * @param int|null $langId Null fetches all languages (returns array keyed by id_lang)
     * @param ShopConstraint $shopConstraint Shop context — determines which row to read
     * @param bool $isLangMultishop Whether lang scope is shop-aware
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
        bool $isLangMultishop = false,
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
        bool $isLangMultishop = false,
        ?ExtraPropertyDefinitionCollection $definitions = null,
    ): array;
}
