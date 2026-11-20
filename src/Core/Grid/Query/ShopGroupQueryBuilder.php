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

final class ShopGroupQueryBuilder extends AbstractDoctrineQueryBuilder
{
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $shopCount = $this->connection->createQueryBuilder()
            ->select('COUNT(s.id_shop)')
            ->from($this->dbPrefix . 'shop', 's')
            ->where('s.id_shop_group = sg.id_shop_group');

        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->select('sg.id_shop_group, sg.name')
            ->addSelect('(' . $shopCount->getSQL() . ') AS shop_count');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria)
            ->select('COUNT(sg.id_shop_group)');
    }

    private function getBaseQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'shop_group', 'sg')
            ->where('sg.deleted = 0');

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('id_shop_group' === $filterName) {
                $qb->andWhere('sg.id_shop_group = :id_shop_group')
                    ->setParameter('id_shop_group', $filterValue);
            } elseif ('name' === $filterName) {
                $qb->andWhere('sg.name LIKE :name')
                    ->setParameter('name', '%' . $filterValue . '%');
            }
        }

        return $qb;
    }
}
