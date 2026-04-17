<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ApiClientFilters;

/**
 * Class ApiClientQueryBuilder builds search & count queries for api access grid.
 */
class ApiClientQueryBuilder extends AbstractDoctrineQueryBuilder
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
        if (!$searchCriteria instanceof ApiClientFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ApiClientFilters::class, get_class($searchCriteria)
                )
            );
        }

        $queryBuilder = $this->getQueryBuilder()
            ->select('aa.*')
            ->from($this->dbPrefix . 'api_client', 'aa');

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
        if (!$searchCriteria instanceof ApiClientFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ApiClientFilters::class, get_class($searchCriteria)
                )
            );
        }

        return $this->getQueryBuilder()
            ->select('COUNT(aa.id_api_client)')
            ->from($this->dbPrefix . 'api_client', 'aa');
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
