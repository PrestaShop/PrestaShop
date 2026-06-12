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

class CustomerGroupsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private int $languageId;
    private DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $languageId
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->languageId = $languageId;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->getCustomerGroupsQueryBuilder($searchCriteria);

        $defaultGroupsSubquery = sprintf(
            'SELECT value FROM %sconfiguration WHERE name IN (\'PS_UNIDENTIFIED_GROUP\', \'PS_GUEST_GROUP\', \'PS_CUSTOMER_GROUP\')',
            $this->dbPrefix
        );

        $builder
            ->select('g.id_group, gl.name, g.reduction, COUNT(cg.id_customer) AS members, g.show_prices, g.date_add')
            ->addSelect(sprintf('IF(g.id_group IN (%s), 1, 0) AS is_default_group', $defaultGroupsSubquery))
            ->leftJoin('g', $this->dbPrefix . 'customer_group', 'cg', 'g.id_group = cg.id_group')
            ->groupBy('g.id_group')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getCustomerGroupsQueryBuilder($searchCriteria)->select('COUNT(DISTINCT g.id_group)');
    }

    private function getCustomerGroupsQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'group', 'g')
            ->innerJoin('g', $this->dbPrefix . 'group_lang', 'gl', 'g.id_group = gl.id_group AND gl.id_lang = :language')
            ->setParameter('language', $this->languageId)
        ;

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria): void
    {
        $filters = $searchCriteria->getFilters();

        if (isset($filters['id_group'])) {
            $builder->andWhere('g.id_group = :id_group')->setParameter('id_group', $filters['id_group']);
        }
        if (isset($filters['name'])) {
            $builder->andWhere('gl.name LIKE :name')->setParameter('name', '%' . $filters['name'] . '%');
        }
        if (isset($filters['reduction'])) {
            $builder->andWhere('g.reduction LIKE :reduction')->setParameter('reduction', '%' . $filters['reduction'] . '%');
        }
        if (isset($filters['show_prices'])) {
            $builder->andWhere('g.show_prices = :show_prices')->setParameter('show_prices', $filters['show_prices']);
        }
        if (isset($filters['date_add']['from'])) {
            $builder->andWhere('g.date_add >= :date_add_from')->setParameter('date_add_from', sprintf('%s 0:0:0', $filters['date_add']['from']));
        }
        if (isset($filters['date_add']['to'])) {
            $builder->andWhere('g.date_add <= :date_add_to')->setParameter('date_add_to', sprintf('%s 23:59:59', $filters['date_add']['to']));
        }
    }
}
