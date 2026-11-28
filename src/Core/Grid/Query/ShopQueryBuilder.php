<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class ShopQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const URL_EXPRESSION = 'CONCAT(\'http://\', su.domain, su.physical_uri, su.virtual_uri)';

    public function __construct(
        Connection $connection,
        string $dbPrefix,
        private readonly DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        private readonly LanguageContext $languageContext,
    ) {
        parent::__construct($connection, $dbPrefix);
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $customers = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'customer', 'c')
            ->where('c.id_shop = s.id_shop');
        $orders = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'orders', 'o')
            ->where('o.id_shop = s.id_shop');

        $qb = $this->getBaseQueryBuilder($searchCriteria)
            ->select('s.id_shop, s.name, sg.name AS shop_group_name, cl.name AS category_name')
            ->addSelect(self::URL_EXPRESSION . ' AS url')
            ->addSelect('(EXISTS(' . $customers->getSQL() . ') OR EXISTS(' . $orders->getSQL() . ')) AS has_dependencies');

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getBaseQueryBuilder($searchCriteria)
            ->select('COUNT(s.id_shop)');
    }

    private function getBaseQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'shop', 's')
            ->leftJoin('s', $this->dbPrefix . 'shop_group', 'sg', 'sg.id_shop_group = s.id_shop_group')
            ->leftJoin(
                's',
                $this->dbPrefix . 'category_lang',
                'cl',
                'cl.id_category = s.id_category AND cl.id_shop = s.id_shop AND cl.id_lang = :languageId'
            )
            ->leftJoin('s', $this->dbPrefix . 'shop_url', 'su', 'su.id_shop = s.id_shop AND su.main = 1')
            ->where('s.deleted = 0')
            ->setParameter('languageId', $this->languageContext->getId());

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('id_shop' === $filterName) {
                $qb->andWhere('s.id_shop = :id_shop')
                    ->setParameter('id_shop', $filterValue);
            } elseif ('name' === $filterName) {
                $qb->andWhere('s.name LIKE :name')
                    ->setParameter('name', '%' . $filterValue . '%');
            } elseif ('shop_group_name' === $filterName) {
                $qb->andWhere('sg.name LIKE :shop_group_name')
                    ->setParameter('shop_group_name', '%' . $filterValue . '%');
            } elseif ('category_name' === $filterName) {
                $qb->andWhere('cl.name LIKE :category_name')
                    ->setParameter('category_name', '%' . $filterValue . '%');
            } elseif ('url' === $filterName) {
                $qb->andWhere(self::URL_EXPRESSION . ' LIKE :url')
                    ->setParameter('url', '%' . $filterValue . '%');
            }
        }

        return $qb;
    }
}
