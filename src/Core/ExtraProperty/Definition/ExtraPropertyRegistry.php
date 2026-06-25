<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManagerInterface;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Write-only registry implementation: register/unregister extra property definitions.
 *
 * Orchestrates:
 *   - ExtraPropertyDefinitionRepositoryInterface (read) for pre-flight existence checks
 *   - ExtraPropertyDefinitionWriterInterface for definition persistence (save/delete)
 *   - ExtraPropertySchemaManagerInterface for DDL on *_extra / *_extra_lang / *_extra_shop tables
 *
 * Cache invalidation is not handled here: the injected definition writer
 * (CachedExtraPropertyDefinitionRepository in production) invalidates the
 * definitions cache on every save/delete.
 */
class ExtraPropertyRegistry implements ExtraPropertyRegistryInterface
{
    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $readRepository,
        protected readonly ExtraPropertyDefinitionWriterInterface $writeRepository,
        protected readonly ExtraPropertySchemaManagerInterface $schemaManager,
        protected readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritdoc}
     *
     * Constructor-level validations (entityName, propertyName, associatedForms/Grids format,
     * labelWording, moduleName format, storageColumnName length) are enforced by ExtraPropertyDefinition itself.
     *
     * The registry additionally validates:
     * - scope-uniqueness: a module cannot register the same propertyName in two different scopes for the same entity
     * - destructive schema changes on already-registered definitions are refused (see hasStorageChanges())
     *
     * Non-destructive schema changes (defaultValue change, STRING size increase, nullable
     * relaxing, CHOICE enum value addition) are applied to the live column by the schema
     * manager: ensureExtraTableAndColumn() syncs the column definition the same way it
     * syncs the index.
     *
     * Operation order: validate changes → persist to DB → create/alter DDL.
     * Note: if DDL fails after DB persistence, a retry of register() re-attempts the DDL.
     * Destructive changes (type/scope change, size decrease, nullable tightening, enum value
     * removal) require unregister() + register() — automatic data migration is not supported.
     */
    public function register(ExtraPropertyDefinition $definition): bool
    {
        $entityName = $definition->getEntityName();
        $propertyName = $definition->getPropertyName();
        // getModuleName() is already normalized: null for core fields ('' / '_core' inputs included).
        $moduleName = $definition->getModuleName();

        $scope = $definition->getScope();
        $normalizedScope = $scope->value;

        // 1. (entity, module, property) is unique across scopes — a single lookup covers both
        // the scope-uniqueness rule and the immutability check on an existing definition.
        $existingDefinition = $this->readRepository->findDefinitionByModuleAndField(
            $entityName,
            $moduleName,
            $propertyName
        );

        if (null !== $existingDefinition && $existingDefinition->getScope() !== $scope) {
            $this->logger->error(
                'Cannot register extra property {entity}.{field}: already registered with scope "{existing_scope}", cannot also register with scope "{new_scope}".',
                ['entity' => $entityName, 'field' => $propertyName, 'existing_scope' => $existingDefinition->getScope()->value, 'new_scope' => $normalizedScope]
            );

            return false;
        }

        // 2. Refuse destructive schema changes on an existing definition.
        if (null !== $existingDefinition && $this->hasStorageChanges($definition, $existingDefinition)) {
            $this->logger->error(
                'Refusing destructive schema change (type/scope change, size decrease, nullable tightening, enum value removal) for existing extra property {entity}.{field}.',
                ['entity' => $entityName, 'field' => $propertyName]
            );

            return false;
        }

        // 3. Insert or update the registry row.
        $savedId = $this->writeRepository->save($definition);

        if (false === $savedId) {
            return false;
        }

        // 4. Ensure the *_extra table and column exist and match the definition: the schema
        //    manager also syncs remaining non-destructive changes on the live column
        //    (DDL after DB write).
        try {
            $this->schemaManager->ensureExtraTableAndColumn($definition);
        } catch (Throwable $exception) {
            $this->logger->error(
                'Failed to create or alter extra table/column: {message}',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * The stored definition is resolved first (entity + module + property are unique
     * across scopes) and used for both the DDL drop and the registry deletion, so the
     * caller's definition does not need an accurate scope (a wrong scope would
     * otherwise target the wrong *_extra table for the column drop).
     */
    public function unregister(ExtraPropertyDefinition $definition, bool $dropColumn = false): bool
    {
        $storedDefinition = $this->readRepository->findDefinitionByModuleAndField(
            $definition->getEntityName(),
            $definition->getModuleName(),
            $definition->getPropertyName()
        );
        if (null === $storedDefinition) {
            // Nothing registered — nothing to delete.
            return true;
        }

        if ($dropColumn) {
            try {
                $this->schemaManager->dropExtraColumnIfExists($storedDefinition);
            } catch (Throwable $exception) {
                $this->logger->error(
                    'Failed to drop extra column: {message}',
                    ['message' => $exception->getMessage(), 'exception' => $exception]
                );

                return false;
            }
        }

        return $this->writeRepository->deleteByDefinition($storedDefinition);
    }

    /**
     * Returns true when $incoming would change the column schema in a DESTRUCTIVE way,
     * i.e. a change that risks data already stored in the extra column:
     *   - type or scope change (data conversion / storage table move)
     *   - STRING size decrease — truncation risk; effective lengths compared (null ≡ 255)
     *   - nullable tightening (NULL → NOT NULL): existing NULL rows would break the ALTER
     *   - CHOICE enum value removal, or switching between ENUM and the VARCHAR fallback:
     *     values already stored would no longer fit the column
     *
     * Non-destructive changes (defaultValue change, size increase, nullable relaxing, enum
     * value addition) are NOT flagged: the schema manager syncs them onto the live column —
     * see ExtraPropertySchemaManager::syncExtraColumnDefinition(). Display flags, labels,
     * form options, placements, and index type are always freely mutable.
     *
     * nullable / enumValues on $existing are deduced from the live column schema by the
     * repository, so the comparison reflects the actual DDL state.
     */
    protected function hasStorageChanges(ExtraPropertyDefinition $incoming, ExtraPropertyDefinition $existing): bool
    {
        if ($incoming->getType() !== $existing->getType() || $incoming->getScope() !== $existing->getScope()) {
            return true;
        }

        if (ExtraPropertyType::STRING === $incoming->getType()
            && ($incoming->getSize() ?? 255) < ($existing->getSize() ?? 255)
        ) {
            return true;
        }

        if ($existing->isNullable() && !$incoming->isNullable()) {
            return true;
        }

        if (ExtraPropertyType::CHOICE === $incoming->getType()) {
            $existingEnum = $existing->getEnumValues();
            $incomingEnum = $incoming->getEnumValues();
            // ENUM ↔ VARCHAR fallback switch, or any stored literal missing from the new list.
            if ((null === $existingEnum) !== (null === $incomingEnum)) {
                return true;
            }
            if (null !== $existingEnum && [] !== array_diff($existingEnum, $incomingEnum ?? [])) {
                return true;
            }
        }

        return false;
    }
}
