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
 * Class OrderStatesQueryBuilder builds queries to fetch data for order_states grid.
 */
final class OrderStatesQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $searchQueryBuilder = $this->getOrderStatesQueryBuilder($searchCriteria)
            ->select(
                'os.id_order_state',
                'osl.name',
                'os.send_email',
                'os.delivery',
                'os.invoice',
                'osl.template',
                'os.color',
                'os.unremovable'
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
        $countQueryBuilder = $this->getOrderStatesQueryBuilder($searchCriteria)
            ->select('COUNT(*)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getOrderStatesQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'order_state', 'os')
            ->leftJoin(
                'os',
                $this->dbPrefix . 'order_state_lang',
                'osl',
                'os.id_order_state = osl.id_order_state AND osl.id_lang = :context_lang_id'
            )
            ->where('os.deleted = 0')
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
            'id_order_state',
            'name',
            'send_email',
            'delivery',
            'invoice',
            'template',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['send_email', 'delivery', 'invoice', 'id_order_state'])) {
                $qb->andWhere('os.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (in_array($filterName, ['name', 'template'])) {
                $qb->andWhere('osl.`' . $filterName . '` LIKE :' . $filterName);
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
            case 'id_order_state':
            case 'send_email':
            case 'delivery':
            case 'invoice':
                $orderBy = 'os.' . $searchCriteria->getOrderBy();

                break;
            case 'name':
            case 'template':
                $orderBy = 'osl.' . $searchCriteria->getOrderBy();

                break;
            default:
                return;
        }

        $searchQueryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
    }
}
