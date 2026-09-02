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
 * Builds search and count query builders for zone grid.
 */
final class ZoneQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria)
            ->select('z.*')
            ->groupBy('z.id_zone');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getQueryBuilder($searchCriteria)->select('COUNT(DISTINCT z.id_zone)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'zone', 'z')
            ->innerJoin(
                'z',
                $this->dbPrefix . 'zone_shop',
                'zs',
                'z.id_zone = zs.id_zone'
            )
            ->where('zs.id_shop IN (:contextShopIds)')
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($qb, $searchCriteria);

        return $qb;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $criteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $criteria): void
    {
        $allowedFilters = [
            'id_zone',
            'name',
            'active',
        ];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_zone', 'active'])) {
                $builder->andWhere('z.' . $filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            $builder->andWhere('z.' . $filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
