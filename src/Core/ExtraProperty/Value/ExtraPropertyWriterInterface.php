<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;

/**
 * Writes extra property values for a given entity instance.
 *
 * Used by ObjectModel (via ServiceLocator) when persisting extra property values,
 * and by the BackOffice form persister and Admin API for bulk writes.
 */
interface ExtraPropertyWriterInterface
{
    /**
     * Persists extra property values for one entity instance (all scopes in one call).
     *
     * Values are grouped the same way the reader returns them — by module then property
     * name — and the writer routes each value to the table matching the property's
     * registered scope (storage column names stay internal to the writer):
     *
     * [
     *     'module_technical_name' => [
     *         'common_property' => 'value',
     *         'lang_property' => [1 => 'en value', 2 => 'fr value'],   // or scalar, see $defaultLangId
     *         'shop_property' => 'value',                              // for the constraint's shop
     *     ],
     * ]
     *
     * NULL values are persisted as-is for nullable storage columns and skipped for
     * NOT NULL ones (SQL default applies). Modules/properties without a matching
     * definition are ignored.
     *
     * Per-shop values (SHOP scope, and LANG scope on multilang-multishop entities) follow
     * the ShopConstraint like native ObjectModel fields follow the legacy shop context:
     * a single-shop constraint writes that shop's row; a shop group, all-shops or
     * collection constraint fans out to one row per shop in its scope. SHOP-scope rows
     * follow the native {entity}_shop association rule: broad scopes (group, all shops)
     * only refresh shops the entity is associated with — a shop associated later reads
     * the definition default until the next save — while explicitly named shops (single
     * shop, ShopCollection) always get their row, like native CONTEXT_SHOP inserts.
     * LANG rows cover the full scope regardless of associations, like native
     * lang-multishop writes. Whether the lang table is shop-aware is detected internally
     * from the storage schema. The constraint's strict flag is ignored — extra property
     * storage has no group/global rows, so there is no fallback level to target.
     *
     * @param string $entityName Entity table name (e.g. "product")
     * @param string $primaryKeyName PK column name (e.g. "id_product")
     * @param int $entityId
     * @param array<string, array<string, mixed>> $valuesByModule [moduleKey => [propertyName => value]]
     * @param ShopConstraint $shopConstraint Shops the per-shop values are written for
     * @param int|null $defaultLangId Language used when a lang-scoped value is a scalar; null skips scalar lang values
     */
    public function writeAll(
        string $entityName,
        string $primaryKeyName,
        int $entityId,
        array $valuesByModule,
        ShopConstraint $shopConstraint,
        ?int $defaultLangId = null
    ): void;

    /**
     * Toggles a boolean extra property value for one entity instance.
     *
     * The target value is the inverse of the row identified by the constraint's
     * representative shop (a missing row or NULL toggles to enabled); per-shop
     * properties then get that target UPSERTed for every shop in the constraint's
     * scope, so a group / all-shops toggle uniformizes shops that diverged.
     * The storage primary key column is deduced from the definition's entity name
     * ('id_' + entityName) — callers never carry storage details.
     *
     * @param ExtraPropertyDefinition $definition The boolean property to toggle
     * @param int $entityId
     * @param ShopConstraint $shopConstraint Shops the toggled value is written for
     * @param int|null $langId Language of the toggled row — required for LANG-scoped
     *                         definitions, ignored otherwise
     *
     * @throws InvalidArgumentException when the definition type is not BOOL, or when a
     *                                  LANG-scoped definition is toggled without a language id
     */
    public function toggleExtraProperty(
        ExtraPropertyDefinition $definition,
        int $entityId,
        ShopConstraint $shopConstraint,
        ?int $langId = null,
    ): void;

    /**
     * Deletes all extra property rows for one entity instance (all three scopes).
     *
     * Safe to call even if no extra properties are registered: tables that do not
     * exist yet are silently skipped.
     *
     * @param string $entityName Entity table name (e.g. "product")
     * @param string $primaryKeyName PK column name (e.g. "id_product")
     * @param int $entityId
     */
    public function deleteAll(string $entityName, string $primaryKeyName, int $entityId): void;

    /**
     * Deletes the extra property rows belonging to the given shops for one entity
     * instance — the per-shop counterpart of {@see deleteAll()}, used when an entity is
     * removed from some shops but survives on others (ObjectModel::delete() in a partial
     * multishop context).
     *
     * Only per-shop storage is touched: the {entity}_extra_shop rows, and the
     * {entity}_extra_lang rows when the entity's lang table is shop-aware. COMMON values
     * and non-multishop lang rows are shared with the surviving shops and are kept.
     *
     * @param string $entityName Entity table name (e.g. "product")
     * @param string $primaryKeyName PK column name (e.g. "id_product")
     * @param int $entityId
     * @param int[] $shopIds Shops whose rows must be removed (non-positive ids are ignored)
     */
    public function deleteForShops(string $entityName, string $primaryKeyName, int $entityId, array $shopIds): void;
}
