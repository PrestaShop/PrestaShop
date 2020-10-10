<?php

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Query builder for image type grid
 */
class ImageTypeQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $criteriaApplicator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $criteriaApplicator
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->criteriaApplicator = $criteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getImageTypeQueryBuilder($searchCriteria)
            ->addSelect('it.id_image_type, it.name, it.width, it.height')
            ->addSelect('it.products, it.categories, it.manufacturers, it.suppliers, it.stores');

        $this->criteriaApplicator
            ->applySorting($searchCriteria, $qb)
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getImageTypeQueryBuilder($searchCriteria)
            ->select('COUNT(*)');
    }

    private function getImageTypeQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'image_type', 'it');

        $this->applyFilters($searchCriteria->getFilters(), $qb);

        return $qb;
    }

    /**
     * Apply filters to image type query builder.
     *
     * @param array $filters
     * @param QueryBuilder $qb
     */
    private function applyFilters(array $filters, QueryBuilder $qb)
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

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('it.name LIKE :' . $filterName);
                $qb->set($filterName, $filterValue);
            }

            $qb->andWhere('it.' . $filterName . ' = :' . $filterName);
            $qb->setParameter($filterName, $filterValue);
        }
    }
}
