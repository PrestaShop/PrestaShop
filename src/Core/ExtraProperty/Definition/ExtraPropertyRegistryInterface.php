<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

/**
 * Write interface for extra property definitions: register and unregister operations.
 *
 * Deliberately does NOT extend ExtraPropertyDefinitionRepositoryInterface.
 * Read and write concerns are kept separate.
 *
 * Implementations are responsible for persisting the definition row AND ensuring the
 * corresponding SQL column exists in the entity's *_extra table, and for invalidating
 * any read cache after a successful write.
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
     * @return int|false The registry row id on success (insert or update), false on failure
     */
    public function register(ExtraPropertyDefinition $definition): int|false;

    /**
     * Unregister an extra property definition.
     * When $dropColumn is true, the physical SQL column is also dropped.
     *
     * @param ExtraPropertyDefinition $definition Definition identifying the property to unregister (entityName, propertyName, moduleName and scope are used as the lookup key)
     * @param bool $dropColumn
     *
     * @return bool
     */
    public function unregister(ExtraPropertyDefinition $definition, bool $dropColumn = false): bool;
}
