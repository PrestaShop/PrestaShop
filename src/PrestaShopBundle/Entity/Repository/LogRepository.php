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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Retrieve Logs data from database.
 * This class should not be used as a Grid query builder. @see LogQueryBuilder
 */
class LogRepository implements RepositoryInterface, DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var string
     */
    private $databasePrefix;
    /**
     * @var string
     */
    private $logTable;

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    public function __construct(
        Connection $connection,
        $databasePrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->logTable = $this->databasePrefix . 'log';
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT l.* FROM $this->logTable l");

        return $statement->fetchAll();
    }

    /**
     * Get all logs with employee name and avatar information SQL query.
     *
     * @param array $filters
     *
     * @return string the SQL query
     */
    public function findAllWithEmployeeInformationQuery($filters)
    {
        $queryBuilder = $this->getAllWithEmployeeInformationQuery($filters);

        $query = $queryBuilder->getSQL();
        $parameters = $queryBuilder->getParameters();

        foreach ($parameters as $pattern => $value) {
            $query = str_replace(":$pattern", $value, $query);
        }

        return $query;
    }

    /**
     * Get all logs with employee name and avatar information.
     *
     * @param array $filters
     *
     * @return array the list of logs
     */
    public function findAllWithEmployeeInformation($filters)
    {
        $queryBuilder = $this->getAllWithEmployeeInformationQuery($filters);
        $statement = $queryBuilder->execute();

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get a reusable Query Builder to dump and execute SQL.
     *
     * @param array $filters
     *
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getAllWithEmployeeInformationQuery($filters)
    {
        $employeeTable = $this->databasePrefix . 'employee';
        $queryBuilder = $this->connection->createQueryBuilder();
        $wheres = array_filter($filters['filters'], function ($value) {
            return !empty($value);
        });
        $scalarFilters = array_filter($wheres, function ($key) {
            return !in_array($key, ['date_from', 'date_to', 'employee'], true);
        }, ARRAY_FILTER_USE_KEY);

        $qb = $queryBuilder
            ->select('l.*', 'e.email', 'CONCAT(e.firstname, \' \', e.lastname) as employee')
            ->from($this->logTable, 'l')
            ->innerJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee')
            ->orderBy($filters['orderBy'], $filters['sortOrder'])
            ->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        if (!empty($scalarFilters)) {
            foreach ($scalarFilters as $column => $value) {
                $qb->andWhere("$column LIKE :$column");
                $qb->setParameter($column, '%' . $value . '%');
            }
        }

        /* Manage Dates interval */
        if (!empty($wheres['date_from']) && !empty($wheres['date_to'])) {
            $qb->andWhere('l.date_add BETWEEN :date_from AND :date_to');
            $qb->setParameters([
                'date_from' => $wheres['date_from'],
                'date_to' => $wheres['date_to'],
            ]);
        }

        /* Manage Employee filter */
        if (!empty($wheres['employee'])) {
            $qb->andWhere('e.lastname LIKE :employee OR e.firstname LIKE :employee');
            $qb->setParameter('employee', '%' . $wheres['employee'] . '%');
        }

        return $qb;
    }

    /**
     * Delete all logs.
     *
     * @return int the number of affected rows
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteAll()
    {
        $platform = $this->connection->getDatabasePlatform();

        return $this->connection->executeUpdate($platform->getTruncateTableSQL($this->logTable, true));
    }

    /**
     * Get query that searches grid rows.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @deprecated deprecated since 1.7.8.0
     * @see LogQueryBuilder::getSearchQueryBuilder
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        @trigger_error(sprintf('The "%s()" method is deprecated since 1.7.8.0', __METHOD__), E_USER_DEPRECATED);

        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('l.*', 'e.email', 'CONCAT(e.firstname, \' \', e.lastname) as employee');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * Get query that counts grid rows.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @deprecated deprecated since 1.7.8.0
     * @see LogQueryBuilder::getCountQueryBuilder
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        @trigger_error(sprintf('The "%s()" method is deprecated since 1.7.8.0', __METHOD__), E_USER_DEPRECATED);

        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('COUNT(*)');

        return $qb;
    }

    /**
     * Build query body without select, sorting & limiting.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @deprecated deprecated since 1.7.8.0
     * @see LogQueryBuilder::buildGridQuery
     *
     * @return QueryBuilder
     */
    private function buildGridQuery(SearchCriteriaInterface $searchCriteria)
    {
        @trigger_error(sprintf('The "%s()" method is deprecated since 1.7.8.0', __METHOD__), E_USER_DEPRECATED);

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

        $employeeTable = $this->databasePrefix . 'employee';

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->logTable, 'l')
            ->leftJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee');

        $filters = $searchCriteria->getFilters();
        foreach ($filters as $filterName => $filterValue) {
            if (empty($filterValue)) {
                continue;
            }
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('employee' == $filterName) {
                $qb->andWhere(
                    'e.lastname LIKE :employee
                    OR e.firstname LIKE :employee
                    OR CONCAT(e.firstname, \' \', e.lastname) LIKE :employee
                    OR CONCAT(e.lastname, \' \', e.firstname) LIKE :employee'
                );
                $qb->setParameter('employee', '%' . $filterValue . '%');

                continue;
            }

            if ('date_add' == $filterName) {
                if (!empty($filterValue['from']) &&
                    !empty($filterValue['to'])
                ) {
                    $qb->andWhere('l.date_add >= :date_from AND l.date_add <= :date_to');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }

        return $qb;
    }
}
