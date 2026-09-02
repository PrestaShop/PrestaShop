<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for disabled product list data
 */
final class DisabledProductQueryBuilder extends AbstractProductQueryBuilder
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
        $qb->andWhere('ps.active = 0');

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $qb->andWhere('ps.id_shop = :context_shop_id')
                ->setParameter('context_shop_id', $this->contextShopId);
        }

        return $qb;
    }
}
