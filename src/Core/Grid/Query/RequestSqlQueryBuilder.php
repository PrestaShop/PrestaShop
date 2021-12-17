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
 * Class RequestSqlQueryBuilder builds search & count queries for RequestSql grid.
 */
final class RequestSqlQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var string
     */
    private $requestSqlTable;

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
        $this->requestSqlTable = $dbPrefix . 'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria = null): QueryBuilder
    {
        $searchQueryBuilder = $this->buildQueryBySearchCriteria($searchCriteria);

        return $searchQueryBuilder
            ->select('rs.*')
            ->orderBy(sprintf('`%s`', $searchCriteria->getOrderBy()), $searchCriteria->getOrderWay())
            ->setFirstResult($searchCriteria->getOffset())
            ->setMaxResults($searchCriteria->getLimit());
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
     * Build partial query by search criteria.
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
            $qb->setParameter($filterName, '%' . $value . '%');
        }

        return $qb;
    }
}
