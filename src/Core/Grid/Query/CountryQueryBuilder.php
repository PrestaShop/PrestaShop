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
class CountryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    protected $searchCriteriaApplicator;

    /**
     * @var int[]
     */
    protected $contextShopIds;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds,
        int $contextLangId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria)
            ->select('c.id_country, c.iso_code, c.call_prefix, c.active, cl.name, z.name as zone_name');

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
        return $this->getQueryBuilder($searchCriteria)->select('COUNT(DISTINCT c.id_country)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'country', 'c')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'country_lang',
                'cl',
                'cl.id_country = c.id_country AND cl.id_lang = (:contextLangId)'
            )
            ->innerJoin(
                'c',
                $this->dbPrefix . 'zone',
                'z',
                'z.id_zone = c.id_zone'
            )
            ->innerJoin(
                'c',
                $this->dbPrefix . 'country_shop',
                'cs',
                'c.id_country = cs.id_country AND cs.id_shop in (:contextShopIds)'
            )
            ->setParameter('contextLangId', $this->contextLangId)
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->groupBy('c.id_country')
        ;

        $this->applyFilters($qb, $searchCriteria);

        return $qb;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $criteria
     */
    protected function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $criteria): void
    {
        $allowedFilters = ['id_country', 'name', 'iso_code', 'call_prefix', 'zone_name', 'active'];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_country', 'active'])) {
                $builder->andWhere('c.' . $filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'name') {
                $builder->andWhere('cl.' . $filterName . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'zone_name') {
                $builder->andWhere('z.name LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if (in_array($filterName, ['iso_code', 'call_prefix'])) {
                $builder->andWhere('c.' . $filterName . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
            }
        }
    }
}
