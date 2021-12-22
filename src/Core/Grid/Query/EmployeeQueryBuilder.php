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
 * Class EmployeeQueryBuilder builds queries for Employees grid.
 */
final class EmployeeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var string
     */
    private $contextIdLang;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param string $contextIdLang
     * @param int[] $contextShopIds
     */
    public function __construct(
        Connection $connection,
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        $contextIdLang,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextIdLang = $contextIdLang;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $searchQueryBuilder = $this->getEmployeeQueryBuilder($searchCriteria)
            ->select('e.*, pl.name as profile_name');

        $this->searchCriteriaApplicator->applyPagination($searchCriteria, $searchQueryBuilder);
        $this->applySorting($searchCriteria, $searchQueryBuilder);

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $countQueryBuilder = $this->getEmployeeQueryBuilder($searchCriteria)
            ->select('COUNT(e.id_profile)');

        return $countQueryBuilder;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getEmployeeQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $sub = $this->connection->createQueryBuilder()
            ->select('1')
            ->from($this->dbPrefix . 'employee_shop', 'es')
            ->where('e.id_employee = es.id_employee')
            ->andWhere('es.id_shop IN (:context_shop_ids)');

        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'employee', 'e')
            ->leftJoin(
                'e',
                $this->dbPrefix . 'profile_lang',
                'pl',
                'e.id_profile = pl.id_profile AND pl.id_lang = ' . (int) $this->contextIdLang
            )
            ->andWhere('EXISTS (' . $sub->getSQL() . ')')
            ->setParameter('context_shop_ids', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    /**
     * Apply filters for Query builder.
     *
     * @param QueryBuilder $queryBuilder
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $queryBuilder, array $filters)
    {
        $allowedFilters = [
            'id_employee',
            'firstname',
            'lastname',
            'email',
            'profile',
            'active',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_employee' === $filterName) {
                $queryBuilder->andWhere('e.id_employee = :' . $filterName);
                $queryBuilder->setParameter($filterName, $filterValue);

                continue;
            }

            if ('profile' === $filterName) {
                $queryBuilder->andWhere('pl.id_profile = :id_profile');
                $queryBuilder->setParameter('id_profile', $filterValue);

                continue;
            }

            if ('active' === $filterName) {
                $queryBuilder->andWhere('e.active = :active');
                $queryBuilder->setParameter('active', $filterValue);

                continue;
            }

            $queryBuilder->andWhere("`$filterName` LIKE :$filterName");
            $queryBuilder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @param QueryBuilder $queryBuilder
     */
    private function applySorting(SearchCriteriaInterface $searchCriteria, QueryBuilder $queryBuilder)
    {
        if ($searchCriteria->getOrderBy() && $searchCriteria->getOrderWay()) {
            $orderBy = $searchCriteria->getOrderBy();

            if ('profile' === $orderBy) {
                $orderBy = 'pl.name';
            }

            $queryBuilder->orderBy($orderBy, $searchCriteria->getOrderWay());
        }
    }
}
