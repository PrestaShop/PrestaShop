<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query\Monitoring;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds query for product without price list data
 */
final class ProductWithoutPriceQueryBuilder extends AbstractProductQueryBuilder
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

        $specPriceSubQuery = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'specific_price', 'sp')
            ->andWhere('p.id_product = sp.id_product');

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $specPriceSubQuery->andWhere('sp.id_shop = :context_shop_id')
                ->setParameter('context_shop_id', $this->contextShopId);
        }

        $qb->andWhere('p.price = 0')
            ->andWhere('p.wholesale_price = 0')
            ->andWhere('NOT EXISTS(' . $specPriceSubQuery->getSQL() . ')');

        return $qb;
    }
}
