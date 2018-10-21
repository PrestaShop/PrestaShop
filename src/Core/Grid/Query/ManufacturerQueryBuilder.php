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

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ManufacturerQueryBuilder is responsible for building queries for manufacturers grid data.
 */
final class ManufacturerQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('m.`id_manufacturer`, m.`name`, m.`active`')
            ->addSelect('COUNT(DISTINCT p.`id_product`) AS `products_count`')
            ->addSelect('COUNT(DISTINCT a.`id_manufacturer`) AS `addresses_count`')
            ->groupBy('m.`id_manufacturer`')
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT m.`id_manufacturer`)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'manufacturer', 'm')
            ->innerJoin(
                'm',
                $this->dbPrefix . 'manufacturer_shop',
                'ms',
                'ms.`id_manufacturer` = m.`id_manufacturer`'
            )
            ->leftJoin(
                'm',
                $this->dbPrefix . 'product',
                'p',
                'm.`id_manufacturer` = p.`id_manufacturer`'
            )
            ->leftJoin(
                'm',
                $this->dbPrefix . 'address',
                'a',
                'a.`id_manufacturer` = m.`id_manufacturer`'
            )
        ;

        $qb->where('ms.`id_shop` IN (:contextShopIds)');

        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        return $qb;
    }
}
