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
