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
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteria;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

final class SearchEngineQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getSearchEngineQueryBuilder($searchCriteria->getFilters())
            ->select('se.id_search_engine, se.server, se.getvar');

        // Create new search criteria if filter is query_key
        if ($searchCriteria->getOrderBy() === 'query_key') {
            $searchCriteria = new SearchCriteria(
                $searchCriteria->getFilters(),
                'getvar',
                $searchCriteria->getOrderWay(),
                $searchCriteria->getOffset(),
                $searchCriteria->getLimit()
            );
        }

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getSearchEngineQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT se.id_search_engine)');
    }

    /**
     * Gets query builder with the common sql used for displaying search engines list and applying filter actions.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getSearchEngineQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'search_engine', 'se');

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array $filters
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFilters = [
            'id_search_engine' => 'se.id_search_engine',
            'server' => 'se.server',
            'query_key' => 'se.getvar',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!array_key_exists($filterName, $allowedFilters)) {
                continue;
            }

            if ($filterName === 'id_search_engine') {
                $qb->andWhere($allowedFilters[$filterName] . ' = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere($allowedFilters[$filterName] . ' LIKE :' . $filterName);
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
