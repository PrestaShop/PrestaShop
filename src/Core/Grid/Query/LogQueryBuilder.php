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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class LogQueryBuilder builds search & count queries for log grid.
 */
final class LogQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicator
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicator $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder()
            ->select('lg.*')
            ->from($this->dbPrefix . 'log', 'lg');

        $this->applyAssociatedQueries($queryBuilder);
        $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $queryBuilder)
            ->applySorting($searchCriteria, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $queryBuilder = $this->getQueryBuilder()
            ->select('COUNT(lg.id_log)')
            ->from($this->dbPrefix . 'log', 'lg');
        $this->applyAssociatedQueries($queryBuilder);

        return $this->applyFilters($searchCriteria->getFilters(), $queryBuilder);
    }

    private function applyAssociatedQueries(QueryBuilder $queryBuilder): void
    {
        $this->appendEmployeeQuery($queryBuilder);
        $this->appendShopQuery($queryBuilder);
        $this->appendLangQuery($queryBuilder);
        $this->appendShopGroupQuery($queryBuilder);
    }

    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * Append "Shop" column to logs query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendShopQuery(QueryBuilder $queryBuilder): void
    {
        $shopQueryBuilder = $this->getQueryBuilder()
            ->select('s.name')
            ->from($this->dbPrefix . 'shop', 's')
            ->where('s.id_shop = lg.id_shop')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $shopQueryBuilder->getSQL() . ') as shop_name');
    }

    /**
     * Append "Shop group" column to logs query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendShopGroupQuery(QueryBuilder $queryBuilder): void
    {
        $shopQueryBuilder = $this->getQueryBuilder()
            ->select('sg.name')
            ->from($this->dbPrefix . 'shop_group', 'sg')
            ->where('sg.id_shop_group = lg.id_shop_group')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $shopQueryBuilder->getSQL() . ') as shop_group_name');
    }

    /**
     * Append "Lang" column to logs query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendLangQuery(QueryBuilder $queryBuilder): void
    {
        $shopQueryBuilder = $this->getQueryBuilder()
            ->select('lng.name')
            ->from($this->dbPrefix . 'lang', 'lng')
            ->where('lng.id_lang = lg.id_lang')
            ->setMaxResults(1);

        $queryBuilder->addSelect('(' . $shopQueryBuilder->getSQL() . ') as language');
    }

    /**
     * Append "Employee" column to logs query builder.
     *
     * @param QueryBuilder $queryBuilder
     */
    private function appendEmployeeQuery(QueryBuilder $queryBuilder): void
    {
        $queryBuilder
            ->addSelect($this->getEmployeeField(false) . ' as employee, e.email')
            ->leftJoin('lg', $this->dbPrefix . 'employee', 'e', 'e.id_employee = lg.id_employee')
        ;
    }

    /**
     * Apply filters to log query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    private function applyFilters(array $filters, QueryBuilder $qb): QueryBuilder
    {
        $allowedFilters = [
            'id_log',
            'employee',
            'severity',
            'message',
            'object_type',
            'object_id',
            'error_code',
            'date_add',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere('lg.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('lg.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            if ('employee' === $filterName) {
                $alias = $this->getEmployeeField(true);

                $qb->andWhere("$alias LIKE :$filterName");
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            $qb->andWhere('`' . $filterName . '` LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }

        return $qb;
    }

    /**
     * @return string
     */
    private function getEmployeeField(bool $includeFullFirstname = true): string
    {
        if ($includeFullFirstname) {
            return 'CONCAT(e.`firstname`, \' \', e.`lastname`)';
        }

        return 'CONCAT(LEFT(e.`firstname`, 1), \'. \', e.`lastname`)';
    }
}
