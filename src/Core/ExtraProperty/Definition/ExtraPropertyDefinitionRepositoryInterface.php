<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;

/**
 * Read-only repository for extra property definitions.
 *
 * Intentionally minimal: two methods only.
 *   - getAllDefinitions() returns the full set; callers use ExtraPropertyDefinitionCollection filter
 *     helpers (filterByEntity, filterByForm, filterByGrid, filterByScope, …) to narrow the result.
 *     This method is cached by CachedExtraPropertyDefinitionRepository.
 *   - findDefinitionByModuleAndField() is a targeted write-path lookup; it is never cached.
 *
 * All read methods return typed ExtraPropertyDefinition value objects or collections.
 */
interface ExtraPropertyDefinitionRepositoryInterface
{
    /**
     * Returns all registered extra property definitions as a typed collection.
     *
     * Use the collection's filter helpers to narrow the result:
     *   - filterByEntity($entityName) — replaces the former getDefinitionCollection($entityName)
     *   - filterByForm($formId)       — replaces getDefinitionCollectionByFormId($formId)
     *   - filterByGrid($gridId)       — replaces getDefinitionCollectionByGridId($gridId)
     *   - filterByScope(), filterByApi(), filterForFrontOffice(), etc.
     */
    public function getAllDefinitions(): ExtraPropertyDefinitionCollection;

    /**
     * Finds one registry definition matching entity, module, and property name.
     *
     * The (entity, module, property) tuple is unique across scopes — the registry
     * refuses to register the same property name under two different scopes — so no
     * scope is needed to identify a definition.
     *
     * Used by the write path (ExtraPropertyRegistry) to check for an existing definition
     * before deciding INSERT vs UPDATE. Never cached — always reflects current DB state.
     *
     * @param string $entityName Normalized entity name
     * @param string|null $moduleName Module technical name, or null for core fields
     * @param string $fieldName Property name
     *
     * @return ExtraPropertyDefinition|null
     */
    public function findDefinitionByModuleAndField(
        string $entityName,
        ?string $moduleName,
        string $fieldName,
    ): ?ExtraPropertyDefinition;

    /**
     * Finds one registry definition by its primary key.
     *
     * Used by the BO registry management feature (read-only access by id, e.g. the "view"
     * page for module-owned definitions, which must remain readable even though it is
     * protected). Never cached — always reflects current DB state, same rationale as
     * findDefinitionByModuleAndField(). Write-path callers should use
     * getUnprotectedDefinitionById() instead, which also enforces the module-owned guard.
     *
     * @param int $id
     *
     * @return ExtraPropertyDefinition|null null when no row matches this id
     */
    public function getDefinitionById(int $id): ?ExtraPropertyDefinition;

    /**
     * Finds one registry definition by its primary key, rejecting module-owned definitions.
     *
     * Centralizes the "not found" / "module owned" guard that every BO write handler
     * (Delete, Edit) needs before mutating a definition, so callers get a single call
     * instead of repeating both checks.
     *
     * @param int $id
     *
     * @throws ExtraPropertyDefinitionNotFoundException When no row matches this id
     * @throws ProtectedModuleExtraPropertyDefinitionException When the definition is owned by a module
     */
    public function getUnprotectedDefinitionById(int $id): ExtraPropertyDefinition;
}
