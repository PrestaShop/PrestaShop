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

final class ShopUrlQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const URL_EXPRESSION = 'CONCAT(\'http://\', su.domain, su.physical_uri, su.virtual_uri)';

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->select('su.id_shop_url, s.name AS shop_name, su.main, su.active')
            ->addSelect(self::URL_EXPRESSION . ' AS url');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria)
            ->select('COUNT(su.id_shop_url)');
    }

    private function getBaseQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'shop_url', 'su')
            ->leftJoin('su', $this->dbPrefix . 'shop', 's', 's.id_shop = su.id_shop');

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if (in_array($filterName, ['id_shop_url', 'main', 'active'], true)) {
                $qb->andWhere('su.' . $filterName . ' = :' . $filterName)
                    ->setParameter($filterName, $filterValue);
            } elseif ('shop_name' === $filterName) {
                $qb->andWhere('s.name LIKE :shop_name')
                    ->setParameter('shop_name', '%' . $filterValue . '%');
            } elseif ('url' === $filterName) {
                $qb->andWhere(self::URL_EXPRESSION . ' LIKE :url')
                    ->setParameter('url', '%' . $filterValue . '%');
            }
        }

        return $qb;
    }
}
