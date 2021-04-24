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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class SupplierQueryBuilder builds search & count queries for suppliers grid.
 */
final class SupplierQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int $contextLangId
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextLangId,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextLangId = $contextLangId;
        $this->contextShopIds = $contextShopIds;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $filters = $searchCriteria->getFilters();
        $isCountFilter = array_key_exists('products_count', $filters);

        if ($isCountFilter) {
            $qb = $this->getQueryBuilderByProductsCount($filters);
            $qb->select('*');
            $this->applyFilters($qb, $filters, 'subQuery');
            $this->applyListQueryParameters($qb);
        } else {
            $qb = $this->getQueryBuilder();
            $this->applyListQuerySelection($qb);
            $this->applyListQueryParameters($qb);
            $this->applyFilters($qb, $filters, 's');
        }

        $this->searchCriteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $filters = $searchCriteria->getFilters();
        $isCountFilter = array_key_exists('products_count', $filters);

        if ($isCountFilter) {
            $alias = 'subQuery';
            $qb = $this->getQueryBuilderByProductsCount($filters);
            $this->applyFilters($qb, $filters, $alias);
            $this->applyListQueryParameters($qb);
        } else {
            $qb = $this->getQueryBuilder();
            $this->applyListQueryParameters($qb);
            $this->applyFilters($qb, $filters, 's');
            $alias = 's';
        }

        $qb->select('COUNT(DISTINCT ' . $alias . '.`id_supplier`)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder()
    {
        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'supplier', 's')
            ->innerJoin(
                's',
                $this->dbPrefix . 'supplier_lang',
                'sl',
                'sl.`id_supplier` = s.`id_supplier`'
            )
            ->innerJoin(
                's',
                $this->dbPrefix . 'supplier_shop',
                'ss',
                'ss.`id_supplier` = s.`id_supplier`'
            )
            ->leftJoin(
                's',
                $this->dbPrefix . 'product_supplier',
                'ps',
                'ps.`id_supplier` = s.`id_supplier`'
            )
            ->andWhere('sl.`id_lang` = :contextLangId')
            ->andWhere('ss.`id_shop` IN (:contextShopIds)')
        ;
    }

    /**
     * Gets query builder by product count which uses the main query as the sub-query in FROM condition.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilderByProductsCount(array $filters)
    {
        $subQuery = $this->getQueryBuilder();
        $this->applyListQuerySelection($subQuery);

        $alias = 'subQuery';

        $qb = $this->connection
            ->createQueryBuilder()
            ->from(
                '(' . $subQuery->getSQL() . ')',
                $alias
            )
            ->where('subQuery.`products_count` = :productsCountFilter')
        ;

        $qb->setParameter('productsCountFilter', $filters['products_count']);

        return $qb;
    }

    /**
     * Adds select and group by statements.
     *
     * @param QueryBuilder $qb
     */
    private function applyListQuerySelection(QueryBuilder $qb)
    {
        $qb
            ->select('s.`id_supplier`, s.`name`, s.`active`')
            ->addSelect('COUNT(DISTINCT ps.`id_product`) AS `products_count`')
            ->groupBy('s.`id_supplier`')
        ;
    }

    /**
     * Sets the parameters which are used in the queries.
     *
     * @param QueryBuilder $qb
     */
    private function applyListQueryParameters(QueryBuilder $qb)
    {
        $qb
            ->setParameter('contextLangId', $this->contextLangId)
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
        ;
    }

    /**
     * Adds filter restrictions.
     *
     * @param QueryBuilder $qb
     * @param array $filters
     * @param string $alias
     */
    private function applyFilters(QueryBuilder $qb, array $filters, $alias)
    {
        $availableFilters = [
            'id_supplier',
            'name',
            'active',
        ];

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $availableFilters, true)) {
                continue;
            }

            if (in_array($filterName, ['id_supplier', 'active'], true)) {
                $qb->andWhere($alias . '.`' . $filterName . '` = :' . $filterName);
                $qb->setParameter($filterName, $value);

                continue;
            }

            $qb->andWhere($alias . '.`name` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $value . '%');
        }
    }
}
