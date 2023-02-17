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
 * Class AuthorizedApplicationQueryBuilder builds search & count queries for log grid.
 *
 * @experimental
 */
final class AuthorizedApplicationQueryBuilder extends AbstractDoctrineQueryBuilder
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
        string $dbPrefix,
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
            ->select('aa.*')
            ->from($this->dbPrefix . 'authorized_application', 'aa');

        $this->applyAssociatedQueries($queryBuilder);

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
            ->select('COUNT(aa.id_authorized_application)')
            ->from($this->dbPrefix . 'authorized_application', 'aa');

        return $queryBuilder;
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

    private function applyAssociatedQueries(QueryBuilder $queryBuilder): void
    {
        $this->appendActiveApiAccessQuery($queryBuilder);
        $this->appendInactiveApiAccessQuery($queryBuilder);
    }

    private function appendActiveApiAccessQuery(QueryBuilder $queryBuilder): void
    {
        $shopQueryBuilder = $this->getQueryBuilder()
            ->select('count(api.active)')
            ->from($this->dbPrefix . 'api_access', 'api')
            ->where('api.id_authorized_application = aa.id_authorized_application')
            ->andWhere('api.active = 1');

        $queryBuilder->addSelect('(' . $shopQueryBuilder->getSQL() . ') as active_api_access');
    }

    private function appendInactiveApiAccessQuery(QueryBuilder $queryBuilder): void
    {
        $shopQueryBuilder = $this->getQueryBuilder()
            ->select('count(api.id_api_access)')
            ->from($this->dbPrefix . 'api_access', 'api')
            ->where('api.id_authorized_application = aa.id_authorized_application')
            ->andWhere('api.active = 0');

        $queryBuilder->addSelect('(' . $shopQueryBuilder->getSQL() . ') as inactive_api_access');
    }
}
