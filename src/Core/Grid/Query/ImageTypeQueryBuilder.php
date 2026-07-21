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
use PrestaShop\PrestaShop\Core\Search\Filters\ImageTypeFilters;

/**
 * Class ImageTypeQueryBuilder builds search & count queries for image type grid.
 */
class ImageTypeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private DoctrineSearchCriteriaApplicator $searchCriteriaApplicator;

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
        if (!$searchCriteria instanceof ImageTypeFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ImageTypeFilters::class, get_class($searchCriteria)
                )
            );
        }

        $queryBuilder = $this->getQueryBuilder($searchCriteria)
            ->select('it.*')
            ->from($this->dbPrefix . 'image_type', 'it');

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
        if (!$searchCriteria instanceof ImageTypeFilters) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected %s, but got %s',
                    ImageTypeFilters::class, get_class($searchCriteria)
                )
            );
        }

        return $this->getQueryBuilder($searchCriteria)
            ->select('COUNT(it.id_image_type)')
            ->from($this->dbPrefix . 'image_type', 'it');
    }

    /**
     * Get generic query builder.
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder();
        $this->applyFilters($qb, $searchCriteria);

        return $qb;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $criteria
     */
    private function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $criteria): void
    {
        $allowedFilters = [
            'id_image_type',
            'name',
            'width',
            'height',
            'products',
            'categories',
            'manufacturers',
            'suppliers',
            'stores',
        ];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ($filterName === 'name') {
                $builder->andwhere('it.' . $filterName . ' like :' . $filterName);
                $builder->setparameter($filterName, '%' . $filterValue . '%');
            } else {
                $builder->andWhere('it.' . $filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
            }
        }
    }
}
