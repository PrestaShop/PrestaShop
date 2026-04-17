<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Query builder builds search & count queries for tax rules group grid.
 */
class TaxRulesGroupQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextShopIds = $contextShopIds;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select('trg.`id_tax_rules_group`, trg.`name`, trg.`active`')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this
            ->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT trg.`id_tax_rules_group`)');
    }

    /**
     * Gets query builder with the common sql used for displaying tax rule groups list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'tax_rules_group', 'trg')
            ->leftJoin(
                'trg',
                $this->dbPrefix . 'tax_rules_group_shop',
                'trgs',
                'trg.`id_tax_rules_group` = trgs.`id_tax_rules_group`'
            );

        $qb->andWhere('trgs.`id_shop` IN (:contextShopIds)')
            ->andWhere('trg.`deleted` = 0')
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Tax rule groups list filtering
     *
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFiltersMap = [
            'id_tax_rules_group' => 'trg.id_tax_rules_group',
            'name' => 'trg.name',
            'active' => 'trg.active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            if (in_array($filterName, ['name', 'id_tax_rules_group'])) {
                $qb
                    ->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $this->escapePercent($value) . '%');

                continue;
            }

            $qb
                ->andWhere($allowedFiltersMap[$filterName] . ' = :' . $filterName)
                ->setParameter($filterName, $value);
        }
    }
}
