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
use PDO;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Class RequestSqlRepository is responsible for retrieving RequestSql data from database
 */
class RequestSqlRepository implements RepositoryInterface, DoctrineQueryBuilderInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var string
     */
    private $requestSqlTable;

    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->requestSqlTable = $dbPrefix.'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT rs.* FROM $this->requestSqlTable rs");

        return $statement->fetchAll();
    }

    /**
     * Get count of all request sql's
     *
     * @return int  Number of request sql rows
     */
    public function getCount()
    {
        $statement = $this->connection->query("SELECT COUNT(rs.id_request_sql) AS c FROM $this->requestSqlTable rs");
        $row = $statement->fetch();

        return (int) $row['c'];
    }

    /**
     * Find all filtered request sql's
     *
     * @param array $filters
     *
     * @return array
     */
    public function findByFilters(array $filters)
    {
        $qb = $this->connection->createQueryBuilder();

        $conditionValues = array_filter($filters['filters'], function ($value) {
            return !empty($value);
        });

        $qb->select('rs.*')
            ->from($this->requestSqlTable, 'rs')
            ->orderBy('`'.$filters['orderBy'].'`', $filters['sortOrder'])
            ->setFirstResult($filters['offset'])
            ->setMaxResults($filters['limit']);

        $scalarFilters = array_filter($conditionValues, function ($key) {
            return in_array($key, ['name', 'sql']);
        }, ARRAY_FILTER_USE_KEY);

        foreach ($scalarFilters as $column => $value) {
            $qb->andWhere("`$column` LIKE :$column");
            $qb->setParameter($column, '%'.$value.'%');
        }

        if (!empty($conditionValues['id_request_sql'])) {
            $qb->andWhere('rs.id_request_sql = :id_request_sql');
            $qb->setParameter('id_request_sql', $conditionValues['id_request_sql']);
        }

        $statement = $qb->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $searchQueryBuilder = $this->buildQueryBySearchCriteria($searchCriteria);

        $searchQueryBuilder
            ->select('rs.*')
            ->orderBy($searchCriteria->getOrderBy(), $searchCriteria->getOrderWay())
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());

        return $searchQueryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria = null)
    {
        $countQueryBuilder = $this->buildQueryBySearchCriteria($searchCriteria);
        $countQueryBuilder->select('COUNT(rs.id_request_sql)');

        return $countQueryBuilder;
    }

    /**
     * Build partial query by search criteria
     *
     * @param SearchCriteriaInterface $criteria
     *
     * @return QueryBuilder
     */
    private function buildQueryBySearchCriteria(SearchCriteriaInterface $criteria)
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->requestSqlTable, 'rs');

        foreach ($criteria->getFilters() as $filterName => $value) {
            if (empty($value)) {
                continue;
            }

            if ('id_request_sql' === $filterName) {
                $qb->andWhere('rs.id_request_sql = :id_request_sql');
                $qb->setParameter('id_request_sql', $value);
                continue;
            }

            $qb->andWhere("`$filterName` LIKE :$filterName");
            $qb->setParameter($filterName, '%'.$value.'%');
        }

        return $qb;
    }
}
