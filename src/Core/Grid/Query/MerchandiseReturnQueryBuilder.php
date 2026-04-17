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
 * Class MerchandiseReturnQueryBuilder builds queries for merchandise returns grid data.
 */
final class MerchandiseReturnQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLanguageId;

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
     * @param int $contextLanguageId
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        int $contextLanguageId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextLanguageId = $contextLanguageId;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getMerchandiseReturnQueryBuilder($searchCriteria);
        $qb
            ->select('r.id_order_return, r.id_order, r.date_add, orsl.name status, ors.color')
            ->groupBy('r.id_order_return');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getMerchandiseReturnQueryBuilder($searchCriteria)
            ->select('COUNT(r.id_order_return)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getMerchandiseReturnQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $queryBuilder = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'order_return', 'r')
            ->leftJoin(
                'r',
                $this->dbPrefix . 'order_return_state',
                'ors',
                'r.state = ors.id_order_return_state'
            )
            ->leftJoin(
                'r',
                $this->dbPrefix . 'order_return_state_lang',
                'orsl',
                'orsl.id_order_return_state = r.state AND orsl.id_lang = :context_language_id'
            )
            ->setParameter('context_language_id', $this->contextLanguageId)
            ->leftJoin(
                'r',
                $this->dbPrefix . 'orders',
                'o',
                'o.id_order = r.id_order'
            )
            ->andWhere('o.id_shop IN (:context_shop_ids)')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        return $queryBuilder;
    }

    /**
     * Apply filters to merchandise returns query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
    {
        $allowedFilters = [
            'id_order_return',
            'id_order',
            'status',
            'date_add',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_order' === $filterName) {
                $qb->andWhere('o.`' . $filterName . '` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('status' === $filterName) {
                $qb->andWhere('orsl.`name` LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere('r.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('r.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $qb->andWhere('`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
