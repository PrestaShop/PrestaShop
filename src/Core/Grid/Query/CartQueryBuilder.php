<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Builds queries for cart grid
 */
final class CartQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters());
        $qb->addSelect('c.id_cart, c.date_add');
        $qb->addSelect('CONCAT(LEFT(cu.firstname, 1), ". ", cu.lastname) AS customer_name');
        $qb->addSelect('ca.name AS carrier_name, c.date_add, IF(con.id_guest, 1, 0) id_guest');
        $qb->addSelect('
            IF (IFNULL(o.id_order, "Non ordered") = "Non ordered",
                IF(TIME_TO_SEC(TIMEDIFF(:current_date, c.date_add)) > 86400, "Abandoned cart", "Non ordered"),
                    o.id_order) AS status
        ');

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery($searchCriteria->getFilters());
        $qb->select('COUNT(c.id_cart)');

        return $qb;
    }

    /**
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getBaseQuery(array $filters)
    {
        $subSql = $this->connection->createQueryBuilder()
            ->select('co.id_guest')
            ->from($this->dbPrefix . 'connections', 'co')
            ->where('TIME_TO_SEC(TIMEDIFF(:current_date, co.date_add)) < 1800')
            ->setMaxResults(1)
        ;

        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'cart', 'c')
            ->leftJoin('c', $this->dbPrefix . 'customer', 'cu', 'cu.id_customer = c.id_customer')
            ->leftJoin('c', $this->dbPrefix . 'carrier', 'ca', 'ca.id_carrier = c.id_carrier')
            ->leftJoin('c', $this->dbPrefix . 'currency', 'cr', 'cr.id_currency = c.id_currency')
            ->leftJoin('c', $this->dbPrefix . 'orders', 'o', 'o.id_cart = c.id_cart')
            ->leftJoin('c', '('.$subSql->getSQL().')', 'con', 'con.id_guest = c.id_guest')
            ->setParameter('current_date', date('Y-m-d H:i:s'))
        ;

        return $qb;

        $strictComparisonFilters = [
            ''
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (isset($strictComparisonFilters[$filterName])) {
                $alias = $strictComparisonFilters[$filterName];

                $qb->andWhere("$alias = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if (isset($likeComparisonFilters[$filterName])) {
                $alias = $likeComparisonFilters[$filterName];

                $qb->andWhere("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($havingLikeComparisonFilters[$filterName])) {
                $alias = $havingLikeComparisonFilters[$filterName];

                $qb->andHaving("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if (isset($dateComparisonFilters[$filterName])) {
                $alias = $dateComparisonFilters[$filterName];

                if (isset($filterValue['from'])) {
                    $name = sprintf('%s_from', $filterName);
                    $qb->andWhere("$alias >= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['from'], '0:0:0'));
                }

                if (isset($filterValue['to'])) {
                    $name = sprintf('%s_to', $filterName);
                    $qb->andWhere("$alias <= :$name");
                    $qb->setParameter($name, sprintf('%s %s', $filterValue['to'], '23:59:59'));
                }

                continue;
            }
        }
    }
}
