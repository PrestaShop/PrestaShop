<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;

/**
 * Write interface for extra property definitions: register and unregister operations.
 *
 * Deliberately does NOT extend ExtraPropertyDefinitionRepositoryInterface.
 * Read and write concerns are kept separate.
 *
 * Implementations are responsible for persisting the definition row AND ensuring the
 * corresponding SQL column exists in the entity's *_extra table, and for invalidating
 * any read cache after a successful write.
 *
 * Failure contract: every hard failure throws (nothing returns false). Implementations
 * must not leave partial state behind on failure:
 *   - a failed CREATION persists nothing (no definition row, no column);
 *   - a failed UPDATE leaves the previous definition intact;
 *   - a failed unregister() never leaves a definition row pointing at a missing column
 *     (worst case is an unreferenced leftover column).
 */
interface ExtraPropertyRegistryInterface
{
    /**
     * Register or update an extra property definition.
     *
     * The definition must have entityName and propertyName set.
     * When the physical SQL column does not yet exist, it is created.
     * On conflict (same entity+module+field+scope), the definition row is updated.
     *
     * The module name is resolved from $definition->getModuleName(). If null, the field is
     * treated as a core field (no owning module).
     *
     * @param ExtraPropertyDefinition $definition Fully configured definition (entityName and propertyName required)
     *
     * @return int The registry row id (insert or update)
     *
     * @throws ExtraPropertyRegistryException the failure reason is carried by the exception code:
     *                                        SCOPE_CONFLICT, DESTRUCTIVE_SCHEMA_CHANGE, INVALID_FORM_OPTIONS,
     *                                        BASE_TABLE_NOT_FOUND, SCHEMA_FAILURE or PERSISTENCE_FAILURE
     */
    public function register(ExtraPropertyDefinition $definition): int;

    /**
     * Unregister an extra property definition. No-op when nothing matches the
     * (entityName, moduleName, propertyName) lookup key.
     * When $dropColumn is true, the physical SQL column is also dropped.
     *
     * @param ExtraPropertyDefinition $definition Definition identifying the property to unregister (entityName, propertyName and moduleName are used as the lookup key)
     * @param bool $dropColumn
     *
     * @throws ExtraPropertyRegistryException code PERSISTENCE_FAILURE when deleting the definition row fails,
     *                                        SCHEMA_FAILURE when dropping the column fails (the definition row
     *                                        is already removed and the column is left in place)
     */
    public function unregister(ExtraPropertyDefinition $definition, bool $dropColumn = false): void;
}
