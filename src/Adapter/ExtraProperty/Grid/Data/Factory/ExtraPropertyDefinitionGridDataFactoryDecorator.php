<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\ExtraProperty\Grid\Data\Factory;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Enriches the extra property definition grid records with the data the
 * associated_shops column renders: 'associated_shops' (shop names).
 *
 * Per page of records this costs at most three batched queries (association rows,
 * module associations for fallback rows, shop names) — never one query per row.
 *
 * Emitted values follow the availability rules of
 * ExtraPropertyDefinition::isAvailableForShops():
 *  - explicit restriction → those shops;
 *  - module-owned without restriction, module enabled somewhere → the module's shops
 *    (the live fallback);
 *  - otherwise (core-owned, or module with no shop rows) → empty lists, which the
 *    column renders as its empty_label ("All stores").
 */
class ExtraPropertyDefinitionGridDataFactoryDecorator implements GridDataFactoryInterface
{
    public function __construct(
        private readonly GridDataFactoryInterface $extraPropertyDefinitionGridDataFactory,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly FeatureInterface $multistoreFeature,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria): GridData
    {
        $data = $this->extraPropertyDefinitionGridDataFactory->getData($searchCriteria);

        // The associated_shops column only exists when the multistore feature is used.
        if (!$this->multistoreFeature->isUsed()) {
            return $data;
        }

        return new GridData(
            new RecordCollection($this->addShopAssociations($data->getRecords()->all())),
            $data->getRecordsTotal(),
            $data->getQuery()
        );
    }

    /**
     * @param array<int, array<string, mixed>> $records
     *
     * @return array<int, array<string, mixed>>
     */
    private function addShopAssociations(array $records): array
    {
        $definitionIds = array_values(array_filter(array_map(
            static fn (array $record): int => (int) ($record['id_extra_property_definition'] ?? 0),
            $records
        )));
        if ([] === $definitionIds) {
            return $records;
        }

        $explicitShopIds = $this->fetchExplicitShopIds($definitionIds);

        // Module fallback is only needed for module-owned rows WITHOUT an explicit restriction.
        $fallbackModuleNames = [];
        foreach ($records as $record) {
            $definitionId = (int) ($record['id_extra_property_definition'] ?? 0);
            if (!isset($explicitShopIds[$definitionId]) && !empty($record['module_name'])) {
                $fallbackModuleNames[(string) $record['module_name']] = true;
            }
        }
        $moduleShopIds = $this->fetchModuleShopIds(array_keys($fallbackModuleNames));

        $shopNames = $this->fetchShopNames();

        foreach ($records as &$record) {
            $definitionId = (int) ($record['id_extra_property_definition'] ?? 0);
            $shopIds = $explicitShopIds[$definitionId]
                ?? $moduleShopIds[(string) ($record['module_name'] ?? '')]
                ?? [];

            $record['associated_shops'] = array_values(array_map(
                static fn (int $shopId): string => $shopNames[$shopId] ?? (string) $shopId,
                $shopIds
            ));
        }

        return $records;
    }

    /**
     * @param list<int> $definitionIds
     *
     * @return array<int, list<int>> explicit shop ids per definition id (absent = no restriction)
     */
    private function fetchExplicitShopIds(array $definitionIds): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('epds.id_extra_property_definition, epds.id_shop')
            ->from($this->dbPrefix . 'extra_property_definition_shop', 'epds')
            ->where('epds.id_extra_property_definition IN (:definitionIds)')
            ->setParameter('definitionIds', $definitionIds, Connection::PARAM_INT_ARRAY)
            ->orderBy('epds.id_shop', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $shopIdsByDefinition = [];
        foreach ($rows as $row) {
            $shopIdsByDefinition[(int) $row['id_extra_property_definition']][] = (int) $row['id_shop'];
        }

        return $shopIdsByDefinition;
    }

    /**
     * @param list<string> $moduleNames
     *
     * @return array<string, list<int>> enabled shop ids per module name (absent = no shop row at all)
     */
    private function fetchModuleShopIds(array $moduleNames): array
    {
        if ([] === $moduleNames) {
            return [];
        }

        $rows = $this->connection->createQueryBuilder()
            ->select('m.name, ms.id_shop')
            ->from($this->dbPrefix . 'module_shop', 'ms')
            ->innerJoin('ms', $this->dbPrefix . 'module', 'm', 'm.id_module = ms.id_module')
            ->where('m.name IN (:moduleNames)')
            ->setParameter('moduleNames', $moduleNames, Connection::PARAM_STR_ARRAY)
            ->orderBy('ms.id_shop', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $shopIdsByModule = [];
        foreach ($rows as $row) {
            $shopIdsByModule[(string) $row['name']][] = (int) $row['id_shop'];
        }

        return $shopIdsByModule;
    }

    /**
     * @return array<int, string> shop names indexed by shop id (the shop table is tiny)
     */
    private function fetchShopNames(): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('s.id_shop, s.name')
            ->from($this->dbPrefix . 'shop', 's')
            ->executeQuery()
            ->fetchAllAssociative();

        $names = [];
        foreach ($rows as $row) {
            $names[(int) $row['id_shop']] = (string) $row['name'];
        }

        return $names;
    }
}
