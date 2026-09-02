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
 * Builds search & count queries for customer's cart grid.
 */
final class CustomerCartQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());

        $qb
            ->select(
                'c.`id_cart`,
                c.`date_add`'
            )
            ->addSelect('ca.`name` as carrier_name')
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
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT c.`id_cart`)');
    }

    /**
     * Gets query builder with the common sql used for displaying carts list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cart', 'c')
            ->where('c.`id_customer` != 0');

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'carrier',
            'ca',
            'c.`id_carrier` = ca.`id_carrier`'
        );

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to cart query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(QueryBuilder $qb, array $filters)
    {
        $allowedFiltersMap = [
            'id_customer' => 'c.id_customer',
        ];

        foreach ($filters as $filterName => $value) {
            if (!array_key_exists($filterName, $allowedFiltersMap) || empty($value)) {
                continue;
            }

            if ('id_customer' === $filterName) {
                $qb->andWhere('c.`id_customer` = :' . $filterName);
                $qb->setParameter($filterName, $value);
            }
        }
    }
}
