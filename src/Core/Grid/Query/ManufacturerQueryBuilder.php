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
 * Class ManufacturerQueryBuilder is responsible for building queries for manufacturers grid data.
 */
final class ManufacturerQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $addressesQb = $this->connection->createQueryBuilder();
        $addressesQb->select('COUNT(a.`id_manufacturer`) AS `addresses_count`')
            ->from($this->dbPrefix . 'address', 'a')
            ->where('a.`id_manufacturer` != 0')
            ->andWhere('m.`id_manufacturer` = a.`id_manufacturer`')
            ->andWhere('a.`deleted` = 0')
            ->groupBy('a.`id_manufacturer`')
        ;

        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('m.`id_manufacturer`, m.`name`, m.`active`')
            ->addSelect('COUNT(p.`id_product`) AS `products_count`')
            ->addSelect('(' . $addressesQb->getSQL() . ') AS addresses_count')
            ->groupBy('m.`id_manufacturer`')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
            ->applyDeterministicSorting($searchCriteria, $qb, 'm', 'id_manufacturer')
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT m.`id_manufacturer`)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $allowedFilters = ['id_manufacturer', 'name', 'active'];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'manufacturer', 'm')
            ->innerJoin(
                'm',
                $this->dbPrefix . 'manufacturer_shop',
                'ms',
                'ms.`id_manufacturer` = m.`id_manufacturer`'
            )
            ->leftJoin(
                'm',
                $this->dbPrefix . 'product',
                'p',
                'm.`id_manufacturer` = p.`id_manufacturer`'
            )
        ;

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('m.`name` LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }
            $qb->andWhere('m.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }

        $qb->andWhere('ms.`id_shop` IN (:contextShopIds)');

        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        return $qb;
    }
}
