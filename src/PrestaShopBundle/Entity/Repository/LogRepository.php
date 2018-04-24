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

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Retrieve Logs data from database.
 */
class LogRepository implements RepositoryInterface, DoctrineQueryBuilderInterface
{
    private $connection;
    private $databasePrefix;
    private $logTable;

    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->logTable = $this->databasePrefix."log";
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
     * @param array $filters
     * @return string the SQL query
     */
    public function findAllWithEmployeeInformationQuery($filters)
    {
        $queryBuilder = $this->getAllWithEmployeeInformationQuery($filters);

        $query = $queryBuilder->getSql();
        $parameters = $queryBuilder->getParameters();

        foreach ($parameters as $pattern => $value) {
            $query = str_replace(":$pattern", $value, $query);
        }

        return $query;
    }

    /**
     * Get all logs with employee name and avatar information.
     * @param array $filters
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
     * @param array $filters
     * @return \Doctrine\DBAL\Query\QueryBuilder
     */
    public function getAllWithEmployeeInformationQuery($filters)
    {
        $employeeTable = $this->databasePrefix.'employee';
        $queryBuilder = $this->connection->createQueryBuilder();
        $wheres = array_filter($filters['filters'], function ($value) {
            return !empty($value);
        });
        $scalarFilters = array_filter($wheres, function ($key) {
            return !in_array($key, array('date_from', 'date_to', 'employee'));
        },ARRAY_FILTER_USE_KEY);

        $qb = $queryBuilder
            ->select('l.*', 'e.email', 'CONCAT(e.firstname, \' \', e.lastname) as employee')
            ->from($this->logTable, 'l')
            ->innerJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee')
            ->orderBy($filters['orderBy'], $filters['sortOrder'])
            ->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit'])
        ;

        if (!empty($scalarFilters)) {
            foreach ($scalarFilters as $column => $value) {
                $qb->andWhere("$column LIKE :$column");
                $qb->setParameter($column, '%'.$value.'%');
            }
        }

        /* Manage Dates interval */
        if (!empty($wheres['date_from']) && !empty($wheres['date_to'])) {
            $qb->andWhere("l.date_add BETWEEN :date_from AND :date_to");
            $qb->setParameters(array(
               'date_from' => $wheres['date_from'],
               'date_to' => $wheres['date_to'],
            ));
        }

        /* Manage Employee filter */
        if (!empty($wheres['employee'])) {
            $qb->andWhere("e.lastname LIKE :employee OR e.firstname LIKE :employee");
            $qb->setParameter('employee', '%'.$wheres['employee'].'%');
        }

        return $qb;
    }

    /**
     * Delete all logs.
     * @return integer The number of affected rows.
     * @throws \Doctrine\DBAL\DBALException
     */
    public function deleteAll()
    {
        $platform   = $this->connection->getDatabasePlatform();

        return $this->connection->executeUpdate($platform->getTruncateTableSQL($this->logTable, true));
    }

    /**
     * Get query that searches grid rows
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('l.*', 'e.email', 'CONCAT(e.firstname, \' \', e.lastname) as employee')
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        return $qb;
    }

    /**
     * Get query that counts grid rows
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $qb = $this->buildGridQuery($searchCriteria);
        $qb->select('COUNT(*)');

        return $qb;
    }

    /**
     * Build query body without select, sorting & limiting
     *
     * @param SearchCriteriaInterface|null $searchCriteria
     *
     * @return QueryBuilder
     */
    private function buildGridQuery(SearchCriteriaInterface $searchCriteria = null)
    {
        $employeeTable = $this->databasePrefix.'employee';

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->logTable, 'l')
            ->innerJoin('l', $employeeTable, 'e', 'l.id_employee = e.id_employee');

        if (null === $searchCriteria) {
            return $qb;
        }

        $filters = $searchCriteria->getFilters();
        foreach ($filters as $filterName => $filterValue) {
            if (empty($filterValue)) {
                continue;
            }

            if ('employee' == $filterName) {
                $qb->andWhere("e.lastname LIKE :employee OR e.firstname LIKE :employee");
                $qb->setParameter('employee', '%'.$filterValue.'%');
                continue;
            }

            if ('date_add' == $filterName) {
                if (!empty($filterValue['from']) &&
                    !empty($filterValue['to'])
                ) {
                    $qb->andWhere("l.date_add BETWEEN :date_from AND :date_to");
                    $qb->setParameters(array(
                        'date_from' => $filterValue['from'],
                        'date_to' => $filterValue['to'],
                    ));
                }
                continue;
            }

            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%'.$filterValue.'%');
        }

        return $qb;
    }
}
