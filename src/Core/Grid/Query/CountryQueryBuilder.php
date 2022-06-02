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
 * Builds search and count query builders for zone grid.
 */
class CountryQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    protected $searchCriteriaApplicator;

    /**
     * @var int[]
     */
    protected $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int[] $contextShopIds
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
            ->select('c.id_country, c.iso_code, c.call_prefix, c.active, cl.name, z.name as zone_name');

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
        return $this->getQueryBuilder($searchCriteria)->select('COUNT(DISTINCT c.id_country)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    protected function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'country', 'c')
            ->innerJoin(
                'c',
                $this->dbPrefix . 'country_shop',
                'cs',
                'c.id_country = cs.id_country'
            )
            ->innerJoin(
                'c',
                $this->dbPrefix . 'country_lang',
                'cl',
                'cl.id_country = cs.id_country'
            )
            ->leftJoin(
                'c',
                $this->dbPrefix . 'zone',
                'z',
                'z.id_zone = c.id_zone'
            )
            ->where('cs.id_shop IN (:contextShopIds)')
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        $this->applyFilters($qb, $searchCriteria);

        return $qb;
    }

    /**
     * @param QueryBuilder $builder
     * @param SearchCriteriaInterface $criteria
     */
    protected function applyFilters(QueryBuilder $builder, SearchCriteriaInterface $criteria): void
    {
        $allowedFilters = ['id_country', 'name', 'iso_code', 'call_prefix', 'zone_name', 'active'];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['id_country', 'active'])) {
                $builder->andWhere('c.' . $filterName . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'name') {
                $builder->andWhere('cl.' . $filterName . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'zone_name') {
                $builder->andWhere('z.name' . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if (in_array($filterName, ['iso_code', 'call_prefix'])) {
                $builder->andWhere('c.' . $filterName . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
            }
        }
    }
}
