<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds search and count queries for the extra property definition grid.
 *
 * Queries the extra_property_definition registry table with text (LIKE) filters
 * on entity_name, module_name, and property_name, and exact-match filters on
 * type and scope.
 */
final class ExtraPropertyDefinitionQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var string
     */
    protected string $definitionTable;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        protected readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->definitionTable = $dbPrefix . 'extra_property_definition';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->buildBaseQuery($searchCriteria->getFilters());

        $qb->select(
            'epd.id_extra_property_definition',
            'epd.entity_name',
            'epd.module_name',
            'epd.property_name',
            'epd.type',
            'epd.scope',
            'epd.sql_index',
            'epd.display_front'
        );

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'epd', 'id_extra_property_definition')
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->buildBaseQuery($searchCriteria->getFilters())
            ->select('COUNT(epd.id_extra_property_definition)');
    }

    /**
     * Builds the base query with FROM and WHERE clauses applied from active filters.
     *
     * @param array<string, mixed> $filters
     *
     * @return QueryBuilder
     */
    protected function buildBaseQuery(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->definitionTable, 'epd');

        foreach ($filters as $filterName => $value) {
            if ('' === $value || null === $value) {
                continue;
            }

            // Exact-match filters for ENUM columns
            if (in_array($filterName, ['type', 'scope'], true)) {
                $qb->andWhere(sprintf('epd.%s = :%s', $filterName, $filterName));
                $qb->setParameter($filterName, $value);

                continue;
            }

            // LIKE filter for text columns
            if (in_array($filterName, ['entity_name', 'module_name', 'property_name'], true)) {
                $qb->andWhere(sprintf('epd.%s LIKE :%s', $filterName, $filterName));
                $qb->setParameter($filterName, '%' . $value . '%');

                continue;
            }
        }

        return $qb;
    }
}
