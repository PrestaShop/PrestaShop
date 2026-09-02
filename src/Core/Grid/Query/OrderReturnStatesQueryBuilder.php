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
 * Class OrderReturnStatesQueryBuilder builds queries to fetch data for order_states grid.
 */
final class OrderReturnStatesQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     * @param int $contextLangId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator,
        int $contextLangId
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getOrderReturnStatesQueryBuilder($searchCriteria)
            ->select(
                'ors.id_order_return_state',
                'orsl.name',
                'ors.color',
                'IF(ors.id_order_return_state IN (1,2,3,4,5),1,0) AS unremovable'
            );

        $this->applySorting($searchQueryBuilder, $searchCriteria);

        $this->criteriaApplicator->applyPagination(
            $searchCriteria,
            $searchQueryBuilder
        );

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getOrderReturnStatesQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getOrderReturnStatesQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'order_return_state', 'ors')
            ->leftJoin(
                'ors',
                $this->dbPrefix . 'order_return_state_lang',
                'orsl',
                'ors.id_order_return_state = orsl.id_order_return_state AND orsl.id_lang = :context_lang_id'
            )
            ->setParameter('context_lang_id', $this->contextLangId);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to order_states query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_order_return_state',
            'name',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_order_return_state' === $filterName) {
                $qb->andWhere('ors.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('orsl.`' . $filterName . '` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }
        }
    }

    /**
     * Apply sorting so search query builder for order_states.
     *
     * @param QueryBuilder $searchQueryBuilder
     * @param SearchCriteriaInterface $searchCriteria
     */
    private function applySorting(QueryBuilder $searchQueryBuilder, SearchCriteriaInterface $searchCriteria)
    {
        switch ($searchCriteria->getOrderBy()) {
            case 'id_order_return_state':
                $orderBy = 'ors.' . $searchCriteria->getOrderBy();

                break;
            case 'name':
                $orderBy = 'orsl.' . $searchCriteria->getOrderBy();

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
