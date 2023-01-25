<?php

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

class CustomerGroupsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $languageId
     *
     */
    public function __construct(Connection $connection, $dbPrefix, DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator, int $languageId)
    {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->languageId = $languageId;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $builder = $this->getCustomerGroupsQueryBuilder($searchCriteria);

        $builder
            ->select('g.id_group, gl.name, g.reduction, g.price_display_method, g.show_prices')
        ;

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $builder)
            ->applyPagination($searchCriteria, $builder);

        return $builder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getCustomerGroupsQueryBuilder($searchCriteria)->select('COUNT(g.id_group)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getCustomerGroupsQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {

        $builder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'group', 'g')
            ->innerJoin('g', $this->dbPrefix . 'group_lang', 'gl', 'g.id_group = gl.id_group')
            ->andWhere('gl.`id_lang`= :language')
            ->setParameter('language', $this->languageId)
        ;

        $this->applyFilters($builder, $searchCriteria);

        return $builder;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $searchCriteria): void
    {
        $allowedFiltersMap = [
            'id_group' => 'g.id_group',
            'name' => 'gl.name',
        ];

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (!array_key_exists($filterName, $allowedFiltersMap)) {
                continue;
            }

            $builder
                ->andWhere($allowedFiltersMap[$filterName] . ' LIKE :' . $filterName)
                ->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
