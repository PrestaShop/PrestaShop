<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class EmailLogsQueryBuilder is responsible for building queries for email logs grid data
 */
final class EmailLogsQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('m.*, l.name AS lang_name')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(m.id_mail)');

        return $qb;
    }

    /**
     * Get generic query builder
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'mail', 'm')
            ->leftJoin('m', $this->dbPrefix.'lang', 'l', 'm.id_lang = l.id_lang');

        foreach ($filters as $name => $value) {
            if ('id_lang' === $name) {
                $qb->andWhere("l.id_lang = :$name");
                $qb->setParameter($name, $value);

                continue;
            }

            if ('date_add' === $name) {
                if (isset($value['from'])) {
                    $qb->andWhere('m.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s %s', $value['from'], '0:0:0'));
                }

                if (isset($value['to'])) {
                    $qb->andWhere('m.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s %s', $value['to'], '23:59:59'));
                }

                continue;
            }

            $qb->andWhere("$name LIKE :$name");
            $qb->setParameter($name, '%'.$value.'%');
        }

        return $qb;
    }
}
