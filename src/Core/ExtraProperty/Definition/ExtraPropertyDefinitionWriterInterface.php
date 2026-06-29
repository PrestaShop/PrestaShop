<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

/**
 * Write-side repository contract for extra property definitions.
 *
 * Used exclusively by ExtraPropertyRegistry (Core) to persist and remove
 * definitions without depending on the concrete Adapter implementation.
 *
 * Separating this from ExtraPropertyDefinitionRepositoryInterface (read-only)
 * keeps the read path cacheable independently of the write path.
 */
interface ExtraPropertyDefinitionWriterInterface
{
    /**
     * Saves (insert or update) one definition row.
     *
     * The repository resolves the existing row internally from the unique key
     * (entity_name, module_name, property_name — unique across scopes); no external
     * ID is required. Returns the definition id on success, false on failure.
     *
     * @param ExtraPropertyDefinition $definition Typed definition as declared by the module
     *
     * @return int|false
     */
    public function save(ExtraPropertyDefinition $definition): int|false;

    /**
     * Deletes one definition row by primary key.
     *
     * @param int $id
     *
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Deletes one definition row identified by its definition value object.
     *
     * The repository resolves the primary key internally from the definition's
     * (entity_name, module_name, property_name) combination — unique across scopes.
     *
     * @param ExtraPropertyDefinition $definition
     *
     * @return bool False when no matching row is found
     */
    public function deleteByDefinition(ExtraPropertyDefinition $definition): bool;
}
