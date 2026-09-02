<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for product without combination and without quantities list data
 */
final class NoQtyProductWithoutCombinationQueryBuilder extends AbstractProductQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);
        $qb->select('COUNT(DISTINCT p.id_product)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getProductsCommonQueryBuilder($searchCriteria);

        $attrSubQuery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'product_attribute', 'pa')
            ->andWhere('pa.id_product = p.id_product');

        $subQuery = $this->connection->createQueryBuilder();
        $subQuery->select('1')
            ->from($this->dbPrefix . 'stock_available', 'stock')
            ->andWhere('p.id_product = stock.id_product')
            ->andWhere('NOT EXISTS(' . $attrSubQuery->getSQL() . ')')
            ->andWhere('IFNULL(stock.quantity, 0) <= 0');

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $subQuery->andWhere('stock.id_shop = :context_shop_id')
                ->setParameter('context_shop_id', $this->contextShopId);
        }

        $qb->andWhere('EXISTS(' . $subQuery->getSQL() . ')');

        return $qb;
    }
}
