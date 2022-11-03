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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
 * Builds search and count query builders for zone grid.
 */
final class ZoneQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getQueryBuilder($searchCriteria)
            ->select('z.*')
            ->groupBy('z.id_zone');

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
        return $this->getQueryBuilder($searchCriteria)->select('COUNT(DISTINCT z.id_zone)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'zone', 'z')
            ->innerJoin(
                'z',
                $this->dbPrefix . 'zone_shop',
                'zs',
                'z.id_zone = zs.id_zone'
            )
            ->where('zs.id_shop IN (:contextShopIds)')
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

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
            'id_zone',
            'name',
            'active',
        ];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_zone', 'active'])) {
                $builder->andWhere('z.' . $filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            $builder->andWhere('z.' . $filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
