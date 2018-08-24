<?php
/**
 * 2007-2018 PrestaShop.
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

use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class WebserviceQueryBuilder
 */
final class WebserviceQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('wa.`id_webservice_account`, wa.`key`, wa.`description`, wa.`active`')
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
        $qb->select('COUNT(wa.`id_webservice_account`)');
        return $qb;
    }

    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix.'webservice_account', 'wa');

        foreach ($filters as $filterName => $value) {
            if ('active' === $filterName && is_numeric($value)) {
                $qb->andWhere('wa.`active`='.(int) $value);

                continue;
            }

            $qb->andWhere('wa.`'.$filterName.'` LIKE :'.$filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }
}
