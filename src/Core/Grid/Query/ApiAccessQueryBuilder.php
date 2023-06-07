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
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ApiAccessesFilters;

/**
 * Class ApiAccessQueryBuilder builds search & count queries for api access grid.
 */
final class ApiAccessQueryBuilder extends AbstractDoctrineQueryBuilder
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
        if (!$searchCriteria instanceof ApiAccessesFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ApiAccessesFilters::class, get_class($searchCriteria)
                )
            );
        }

        $applicationId = $searchCriteria->getApplicationId();
        $queryBuilder = $this->getQueryBuilder()
            ->select('aa.*')
            ->from($this->dbPrefix . 'api_access', 'aa')
            ->where('aa.id_authorized_application  = :applicationId')
            ->setParameter('applicationId', $applicationId);

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
        if (!$searchCriteria instanceof ApiAccessesFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ApiAccessesFilters::class, get_class($searchCriteria)
                )
            );
        }

        $applicationId = $searchCriteria->getApplicationId();

        return $this->getQueryBuilder()
            ->select('COUNT(aa.id_api_access)')
            ->from($this->dbPrefix . 'api_access', 'aa')
            ->where('aa.id_authorized_application  = :applicationId')
            ->setParameter('applicationId', $applicationId);
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
}
