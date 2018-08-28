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

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class CategoryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLangId
     * @param int $contextShopId
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        $contextLangId,
        $contextShopId
    ) {
        parent::__construct($connection, $dbPrefix);


        $this->contextLangId = $contextLangId;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('c.id_category, c.active, cl.name, cl.description, cs.position')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit())
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(c.id_category)');

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
            ->from($this->dbPrefix . 'category', 'c')
            ->leftJoin(
                'c',
                $this->dbPrefix . 'category_lang',
                'cl',
                'c.id_category = cl.id_category AND cl.id_lang = :context_lang_id AND cl.id_shop = :context_shop_id'
            )
            ->setParameter('context_lang_id', $this->contextLangId)
            ->setParameter('context_shop_id', $this->contextShopId)
            ->leftJoin(
                'c',
                $this->dbPrefix . 'category_shop',
                'cs',
                'c.id_category = cs.id_category AND cs.id_shop = :context_shop_id'
            )
        ;

        foreach ($filters as $filterName => $filterValue) {
            if ('id_category' === $filterName) {
                $qb->andWhere("c.id_category = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere("cl.name LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('description' === $filterName) {
                $qb->andWhere("cl.description LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ('position' === $filterName) {
                $qb->andWhere("cs.position = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('active' === $filterName) {
                $qb->andWhere("c.active = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            if ('id_category_parent' === $filterName) {
                $qb->andWhere("c.id_parent = :$filterName");
                $qb->setParameter($filterName, $filterValue);

                continue;
            }
        }

        return $qb;
    }
}
