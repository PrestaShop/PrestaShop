<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\FormOptionsValidator;
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
        // Required: the registry is only defined in Symfony kernels (services/extra_property/backend.yml),
        // where the form factory is always available — never in the FO legacy container.
        protected readonly FormOptionsValidator $formOptionsValidator,
        protected readonly ShopRepository $shopRepository,
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
     * - formType/formOptions must build a working form field (see FormOptionsValidator) — being the single
     *   write choke point, this covers every path: BO form, CQRS commands and Module::registerExtraProperty()
     * - every shop id in the shop association must exist (the association rows carry no foreign key)
     *
     * Non-destructive schema changes (defaultValue change, STRING size increase, nullable
     * relaxing, CHOICE enum value addition) are applied to the live column by the schema
     * manager: ensureExtraTableAndColumn() syncs the column definition the same way it
     * syncs the index.
     *
     * Operation order: validate changes → create/alter DDL → persist to DB. DDL runs FIRST
     * on purpose: MySQL/MariaDB DDL statements trigger an implicit commit, so a transaction
     * wrapping "row write + DDL" could never roll the row back once the DDL ran. Ordering the
     * single row write last gives the equivalent guarantees instead:
     *   - creation failure persists nothing (no orphan definition row);
     *   - update DDL failure leaves the previous definition row intact;
     *   - a row-write failure after DDL on a CREATION removes the column it just added
     *     (best-effort compensation, gated on the column having actually been added by this
     *     call so pre-existing data is never dropped);
     *   - a row-write failure after DDL on an UPDATE leaves the column synced — harmless,
     *     a retry of register() re-attempts the row write (save() is idempotent).
     * Destructive changes (type/scope change, size decrease, nullable tightening, enum value
     * removal) require unregister() + register() — automatic data migration is not supported.
     *
     * @throws ExtraPropertyRegistryException the failure reason is carried by the exception code:
     *                                        SCOPE_CONFLICT, DESTRUCTIVE_SCHEMA_CHANGE, INVALID_FORM_OPTIONS,
     *                                        UNKNOWN_SHOP, BASE_TABLE_NOT_FOUND, SCHEMA_FAILURE or
     *                                        PERSISTENCE_FAILURE
     */
    public function register(ExtraPropertyDefinition $definition): int
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
            $message = sprintf(
                'Cannot register extra property %s.%s: already registered with scope "%s", cannot also register with scope "%s".',
                $entityName,
                $propertyName,
                $existingDefinition->getScope()->value,
                $normalizedScope
            );
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::SCOPE_CONFLICT);
        }

        // 1b. Refuse a different entity name resolving to the SAME storage (table + column):
        // the DB unique key on (entity_name, module_name, property_name) cannot see across
        // entity spellings, but two definitions writing the same physical column would
        // corrupt each other (an explicit tableName pointing at another entity's table is
        // the reachable case — canonical entity names already converge the known spellings).
        if (null === $existingDefinition) {
            foreach ($this->readRepository->getAllDefinitions()->filterByTableName($definition->getTableName()) as $sibling) {
                if ($sibling->getStorageColumnName() === $definition->getStorageColumnName()
                    && $sibling->getEntityName() !== $entityName
                ) {
                    $message = sprintf(
                        'Cannot register extra property %s.%s: the definition %s.%s already stores its values in the same column ("%s" on the "%s" table). Use one entity name consistently.',
                        $entityName,
                        $propertyName,
                        $sibling->getEntityName(),
                        $sibling->getPropertyName(),
                        $definition->getStorageColumnName(),
                        $definition->getTableName()
                    );
                    $this->logger->error($message);

                    throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::STORAGE_CONFLICT);
                }
            }
        }

        // 2. Refuse destructive schema changes on an existing definition.
        if (null !== $existingDefinition && $this->hasStorageChanges($definition, $existingDefinition)) {
            $message = sprintf(
                'Refusing destructive schema change (type/scope/table change, size decrease, nullable tightening, enum value removal) for existing extra property %s.%s.',
                $entityName,
                $propertyName
            );
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::DESTRUCTIVE_SCHEMA_CHANGE);
        }

        // 3. Refuse a definition whose form field could not be built at render time — being the
        // single write choke point, this covers every path: BO form, CQRS commands and
        // Module::registerExtraProperty(). The errors must reach the human who typed the options.
        $formOptionErrors = $this->formOptionsValidator->validate(
            $definition->getFormType(),
            $definition->getType(),
            $definition->getEnumValues(),
            $definition->getScope(),
            $definition->getFormOptions()
        );
        if ([] !== $formOptionErrors) {
            $message = sprintf('Invalid extra property form options: %s', implode(' ', $formOptionErrors));
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::INVALID_FORM_OPTIONS, errors: $formOptionErrors);
        }

        // 3b. Refuse a defaultValue that does not fit the declared type, BEFORE any DDL: a
        //     malformed DATE default would otherwise be silently erased on hydration, a
        //     non-numeric INT/FLOAT default silently coerced, and a CHOICE default outside
        //     the enum would produce a DDL DEFAULT the column itself refuses.
        if (null !== $definition->getDefaultValue() && !$this->isDefaultValueCompatible($definition)) {
            $message = sprintf(
                'Cannot register extra property %s.%s: default value "%s" is not a valid %s value%s.',
                $entityName,
                $propertyName,
                is_bool($definition->getDefaultValue()) ? var_export($definition->getDefaultValue(), true) : (string) $definition->getDefaultValue(),
                $definition->getType()->value,
                ExtraPropertyType::CHOICE === $definition->getType() && null !== $definition->getEnumValues()
                    ? ' (allowed: ' . implode(', ', $definition->getEnumValues()) . ')'
                    : ''
            );
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::INVALID_DEFAULT_VALUE);
        }

        // 4. Refuse unknown shop ids in the association, BEFORE any DDL or row write: the
        //    association rows carry no foreign key, so an unknown id (e.g. a module calling
        //    registerExtraProperty() with a wrong shop id) would be stored silently and make
        //    the definition invisible on every real shop. Being the single write choke point,
        //    this covers the BO form, the CQRS commands and Module::registerExtraProperty().
        //    null (untouched) and [] (revert to fallback) carry no id to check.
        //    Deliberately validated against EVERY existing shop (getAllShopIds(), no
        //    active/deleted filter): an inactive shop is a legitimate restriction target —
        //    the association is configuration and must survive deactivation/reactivation
        //    cycles. A definition restricted to inactive shops only is simply dormant:
        //    group/all-shops resolution covers usable shops only (ShopListResolver), so it
        //    surfaces nowhere at runtime, while the registry grid's all-shops management
        //    view still lists it for editing.
        $associatedShopIds = $definition->getAssociatedShopIds();
        if (!empty($associatedShopIds)) {
            $unknownShopIds = array_diff($associatedShopIds, $this->shopRepository->getAllShopIds());
            if ([] !== $unknownShopIds) {
                $message = sprintf(
                    'Cannot register extra property %s.%s: unknown shop id(s) %s in the shop association.',
                    $entityName,
                    $propertyName,
                    implode(', ', $unknownShopIds)
                );
                $this->logger->error($message);

                throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::UNKNOWN_SHOP);
            }
        }

        // 5. Ensure the *_extra table and column exist and match the definition: the schema
        //    manager also syncs remaining non-destructive changes on the live column.
        //    DDL runs BEFORE the row write (see the method docblock): a DDL failure here
        //    persists nothing on a creation and leaves the previous row intact on an update.
        try {
            $columnAdded = $this->schemaManager->ensureExtraTableAndColumn($definition);
        } catch (ExtraPropertyException $exception) {
            $this->logger->error(
                'Failed to create or alter extra table/column: {message}',
                ['message' => $exception->getMessage(), 'exception' => $exception]
            );

            throw $exception;
        } catch (Throwable $exception) {
            $message = 'Failed to create or alter extra table/column: ' . $exception->getMessage();
            $this->logger->error($message, ['exception' => $exception]);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::SCHEMA_FAILURE, $exception);
        }

        // 6. Insert or update the registry row.
        $savedId = $this->writeRepository->save($definition);

        if (false === $savedId) {
            // On a creation, remove the column the DDL step just added so the failed
            // registration leaves nothing behind. Gated on $columnAdded: a column that
            // pre-existed this call (e.g. leftover of unregister(dropColumn: false)) may
            // still hold data and is never dropped. Best-effort: the definitive error is
            // the persistence failure below.
            if (null === $existingDefinition && $columnAdded) {
                try {
                    $this->schemaManager->dropExtraColumnIfExists($definition);
                } catch (Throwable $cleanupException) {
                    $this->logger->error(
                        'Failed to clean up extra column after a persistence failure: {message}',
                        ['message' => $cleanupException->getMessage(), 'exception' => $cleanupException]
                    );
                }
            }

            $message = sprintf('Failed to persist the definition row for extra property %s.%s.', $entityName, $propertyName);
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::PERSISTENCE_FAILURE);
        }

        return $savedId;
    }

    /**
     * {@inheritdoc}
     *
     * The stored definition is resolved first (entity + module + property are unique
     * across scopes) and used for both the DDL drop and the registry deletion, so the
     * caller's definition does not need an accurate scope (a wrong scope would
     * otherwise target the wrong *_extra table for the column drop).
     *
     * Operation order: delete the registry row FIRST, drop the column second. A failed
     * column drop then leaves a benign unreferenced column (the definition is already
     * gone, readers never see it) instead of a broken definition pointing at a missing
     * column. Trade-off: retrying unregister() after a failed drop is a no-op (no stored
     * row anymore), the column has to be removed manually — the thrown exception says so.
     *
     * @throws ExtraPropertyRegistryException code PERSISTENCE_FAILURE when deleting the definition row fails,
     *                                        SCHEMA_FAILURE when dropping the column fails (the row is already removed)
     */
    public function unregister(ExtraPropertyDefinition $definition, bool $dropColumn = false): void
    {
        $storedDefinition = $this->readRepository->findDefinitionByModuleAndField(
            $definition->getEntityName(),
            $definition->getModuleName(),
            $definition->getPropertyName()
        );
        if (null === $storedDefinition) {
            // Nothing registered — nothing to delete.
            return;
        }

        if (!$this->writeRepository->deleteByDefinition($storedDefinition)) {
            $message = sprintf(
                'Failed to delete the definition row for extra property %s.%s.',
                $storedDefinition->getEntityName(),
                $storedDefinition->getPropertyName()
            );
            $this->logger->error($message);

            throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::PERSISTENCE_FAILURE);
        }

        if ($dropColumn) {
            try {
                $this->schemaManager->dropExtraColumnIfExists($storedDefinition);
            } catch (ExtraPropertyException $exception) {
                $this->logger->error(
                    'Failed to drop extra column: {message}',
                    ['message' => $exception->getMessage(), 'exception' => $exception]
                );

                throw $exception;
            } catch (Throwable $exception) {
                $message = sprintf(
                    'The extra property definition %s.%s was removed but its column could not be dropped and was left in place: %s',
                    $storedDefinition->getEntityName(),
                    $storedDefinition->getPropertyName(),
                    $exception->getMessage()
                );
                $this->logger->error($message, ['exception' => $exception]);

                throw new ExtraPropertyRegistryException($message, ExtraPropertyRegistryException::SCHEMA_FAILURE, $exception);
            }
        }
    }

    /**
     * Whether the declared defaultValue can actually serve as a default of the declared
     * type. Values may arrive as native scalars (module code, Admin API JSON) or as
     * strings (BO form TextType) — both spellings of a valid value are accepted.
     */
    protected function isDefaultValueCompatible(ExtraPropertyDefinition $definition): bool
    {
        $defaultValue = $definition->getDefaultValue();

        return match ($definition->getType()) {
            ExtraPropertyType::INT => is_int($defaultValue)
                || (is_string($defaultValue) && 1 === preg_match('/^-?\d+$/', $defaultValue)),
            ExtraPropertyType::FLOAT => is_int($defaultValue) || is_float($defaultValue)
                || (is_string($defaultValue) && is_numeric($defaultValue)),
            ExtraPropertyType::BOOL => is_bool($defaultValue) || in_array($defaultValue, [0, 1, '0', '1'], true),
            ExtraPropertyType::DATE => false !== strtotime((string) $defaultValue),
            ExtraPropertyType::CHOICE => null === $definition->getEnumValues()
                || in_array((string) $defaultValue, $definition->getEnumValues(), true),
            ExtraPropertyType::JSON => is_string($defaultValue)
                && (null !== json_decode($defaultValue) || 'null' === trim($defaultValue)),
            default => true,
        };
    }

    /**
     * Returns true when $incoming would change the column schema in a DESTRUCTIVE way,
     * i.e. a change that risks data already stored in the extra column:
     *   - type or scope change (data conversion / storage table move)
     *   - physical table change (a different explicit tableName): the definition would
     *     silently relocate to another {table}_extra, orphaning every stored value
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

        if ($incoming->getTableName() !== $existing->getTableName()) {
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
