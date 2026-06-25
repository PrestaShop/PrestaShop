<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Value;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use Throwable;

/**
 * Reads extra property values from the *_extra / *_extra_lang / *_extra_shop tables.
 *
 * Values are grouped by module technical name then by field name, and returned TYPED:
 * every value is cast from its raw DB string to the declared PHP type via
 * ExtraPropertyValueCaster::castFromDb() (bool/int/float, nullable-aware NULLs).
 * Used by ObjectModel (via ServiceLocator) and front-office LazyArray / presenter contexts.
 */
class ExtraPropertyReader implements ExtraPropertyReaderInterface
{
    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly Connection $connection,
        protected readonly string $prefix,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getExtraProperties(
        string $entityName,
        string $primaryKeyName,
        int $entityId,
        ?int $langId,
        ShopConstraint $shopConstraint,
        bool $isLangMultishop = false,
        ?ExtraPropertyDefinitionCollection $definitions = null,
    ): array {
        if ($entityId <= 0) {
            return [];
        }

        return $this->getMultipleExtraProperties(
            $entityName,
            $primaryKeyName,
            [$entityId],
            $langId,
            $shopConstraint,
            $isLangMultishop,
            $definitions,
        )[$entityId] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleExtraProperties(
        string $entityName,
        string $primaryKeyName,
        array $entityIds,
        ?int $langId,
        ShopConstraint $shopConstraint,
        bool $isLangMultishop = false,
        ?ExtraPropertyDefinitionCollection $definitions = null,
    ): array {
        $entityIds = array_values(array_unique(array_filter(
            array_map('intval', $entityIds),
            static fn (int $id): bool => $id > 0
        )));
        if (empty($entityIds)) {
            return [];
        }

        $allDefinitions = $definitions ?? $this->repository->getAllDefinitions()->filterByEntity($entityName);
        if ($allDefinitions->isEmpty()) {
            return [];
        }

        $shopId = $shopConstraint->isSingleShopContext() ? $shopConstraint->getShopId()->getValue() : null;

        $propertiesByEntity = array_fill_keys($entityIds, []);

        foreach (ExtraPropertyScope::cases() as $scope) {
            $scoped = $allDefinitions->filterByScope($scope);
            if ($scoped->isEmpty()) {
                continue;
            }
            foreach ($this->hydrateExtraPropertiesScope($primaryKeyName, $entityIds, $scope, $scoped, $langId, $shopId, $isLangMultishop) as $entityId => $propertiesByModule) {
                $propertiesByEntity[$entityId] = array_replace_recursive($propertiesByEntity[$entityId] ?? [], $propertiesByModule);
            }
        }

        return $propertiesByEntity;
    }

    /**
     * Fetches extra property values for one scope across several entity ids, with a single query, and returns them
     * grouped by entity id then module name.
     *
     * For LANG scope with $langId = null: all languages are fetched and the value is an array keyed by id_lang —
     * used by BO forms and Admin API single-item reads. For LANG scope with $langId set: a single scalar per field.
     * For SHOP scope: a single scalar for the given shop constraint. COMMON scope: a single scalar per field.
     *
     * Every requested entity id is seeded with the default-valued structure, so an id with no row still appears.
     *
     * @param int[] $entityIds Positive, de-duplicated entity ids
     * @param ExtraPropertyDefinitionCollection $definitions All definitions for this scope (non-empty)
     *
     * @return array<int, array<string, array<string, mixed>>> [entityId => [module_key => [property_name => value]]]
     */
    protected function hydrateExtraPropertiesScope(
        string $primaryKeyName,
        array $entityIds,
        ExtraPropertyScope $fieldScope,
        ExtraPropertyDefinitionCollection $definitions,
        ?int $langId,
        ?int $shopId,
        bool $isLangMultishop,
    ): array {
        $groupByLang = ExtraPropertyScope::LANG === $fieldScope && null === $langId;
        $extraTableName = $this->prefix . $definitions->first()->getExtraTableName();

        // Build a map from DB column name to [module_key, property_name, cast inputs] and the default per property.
        $columnToPropertyMap = [];
        $defaultsByModule = [];
        foreach ($definitions as $definition) {
            $propertyName = $definition->getPropertyName();
            $moduleName = $definition->getNormalizedModuleKey();
            $defaultsByModule[$moduleName][$propertyName] = $groupByLang
                ? []
                : ExtraPropertyValueCaster::castFromDb($definition->getType(), null, $definition->isNullable());

            $columnName = $definition->getStorageColumnName();
            $columnToPropertyMap[$columnName] = [
                'module_name' => $moduleName,
                'property_name' => $propertyName,
                'type' => $definition->getType(),
                'nullable' => $definition->isNullable(),
            ];
        }

        // Seed every requested entity with the default-valued structure (a missing row keeps the defaults).
        $result = array_fill_keys($entityIds, $defaultsByModule);

        // Skip the query when a required id is invalid; the seeded defaults are returned.
        if (ExtraPropertyScope::LANG === $fieldScope && null !== $langId && $langId <= 0) {
            return $result;
        }
        if (ExtraPropertyScope::SHOP === $fieldScope && null !== $shopId && $shopId <= 0) {
            return $result;
        }
        if (ExtraPropertyScope::LANG === $fieldScope && $isLangMultishop && null !== $shopId && $shopId <= 0) {
            return $result;
        }

        $quotedPrimaryKey = $this->connection->quoteIdentifier($primaryKeyName);
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->from($extraTableName, 'extra')
            ->where('extra.' . $quotedPrimaryKey . ' IN (:entityIds)')
            ->setParameter('entityIds', $entityIds, Connection::PARAM_INT_ARRAY);

        // Always select the primary key so rows can be grouped back per entity.
        $selectCols = ['extra.' . $quotedPrimaryKey];
        foreach (array_keys($columnToPropertyMap) as $col) {
            $selectCols[] = 'extra.' . $this->connection->quoteIdentifier($col);
        }

        if (ExtraPropertyScope::LANG === $fieldScope) {
            if ($groupByLang) {
                // Fetch all languages; caller receives an array keyed by id_lang.
                $selectCols[] = 'extra.' . $this->connection->quoteIdentifier('id_lang');
            } else {
                $qb->andWhere('extra.id_lang = :langId')->setParameter('langId', $langId);
            }
            if ($isLangMultishop && null !== $shopId) {
                $qb->andWhere('extra.id_shop = :shopId')->setParameter('shopId', $shopId);
            }
        } elseif (ExtraPropertyScope::SHOP === $fieldScope && null !== $shopId) {
            // Shop scope is always a single scalar value for the given shop constraint.
            $qb->andWhere('extra.id_shop = :shopId')->setParameter('shopId', $shopId);
        }

        $qb->select(...$selectCols);

        try {
            $rows = $qb->executeQuery()->fetchAllAssociative();
        } catch (Throwable) {
            return $result;
        }

        foreach ($rows as $row) {
            $entityId = (int) ($row[$primaryKeyName] ?? 0);
            if (!isset($result[$entityId])) {
                continue;
            }
            $groupKey = $groupByLang ? (int) ($row['id_lang'] ?? 0) : null;
            foreach ($columnToPropertyMap as $columnName => $propertyPath) {
                if (!array_key_exists($columnName, $row)) {
                    continue;
                }
                $value = ExtraPropertyValueCaster::castFromDb($propertyPath['type'], $row[$columnName], $propertyPath['nullable']);
                if (null !== $groupKey) {
                    $result[$entityId][$propertyPath['module_name']][$propertyPath['property_name']][$groupKey] = $value;
                } else {
                    $result[$entityId][$propertyPath['module_name']][$propertyPath['property_name']] = $value;
                }
            }
        }

        return $result;
    }
}
