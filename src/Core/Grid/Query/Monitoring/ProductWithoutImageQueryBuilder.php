<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for product without image list data
 */
final class ProductWithoutImageQueryBuilder extends AbstractProductQueryBuilder
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

        $imageSubQuery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'image_shop', 'img')
            ->andWhere('p.id_product = img.id_product');

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $imageSubQuery->andWhere('img.id_shop = :context_shop_id');
        } else {
            $imageSubQuery->andWhere('img.id_shop = p.id_shop_default');
        }

        $qb->andWhere('NOT EXISTS(' . $imageSubQuery->getSQL() . ')');

        return $qb;
    }
}
