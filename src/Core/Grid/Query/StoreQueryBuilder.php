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

class StoreQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var int[]
     */
    protected $shopIds;

    /**
     * @var int
     */
    protected $languageId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param int[] $shopIds
     * @param int $languageId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $shopIds,
        int $languageId
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->shopIds = $shopIds;
        $this->languageId = $languageId;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->getCommonQueryBuilder($searchCriteria->getFilters())
            ->select('
                s.id_store, sl.name, sl.address1 AS address, s.city, s.postcode,
                state.name AS state, cl.name AS country, s.phone, s.fax, s.active
            ')
            ->groupBy('s.id_store')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        return $this->getCommonQueryBuilder($searchCriteria->getFilters())
            ->select('COUNT(DISTINCT s.id_store)x');
    }

    /**
     * @param array<string, int|string|bool> $filters
     *
     * @return QueryBuilder
     */
    protected function getCommonQueryBuilder(array $filters): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'store', 's')
            ->innerJoin(
                's',
                $this->dbPrefix . 'store_shop',
                'ss',
                's.id_store = ss.id_store AND ss.id_shop IN (:shopIds)'
            )
            ->setParameter('shopIds', $this->shopIds, Connection::PARAM_INT_ARRAY)
            ->innerJoin(
                's',
                $this->dbPrefix . 'store_lang',
                'sl',
                's.id_store = sl.id_store AND sl.id_lang = :langId'
            )
            ->leftJoin(
                's',
                $this->dbPrefix . 'country_lang',
                'cl',
                's.id_country = cl.id_country AND cl.id_lang = :langId'
            )
            ->setParameter('langId', $this->languageId)
            ->leftJoin(
                's',
                $this->dbPrefix . 'state',
                'state',
                's.id_state = state.id_state'
            )
        ;

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param array<string, int|string|bool> $filters
     */
    protected function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $filtersMap = [
            'id_store' => 's.id_store',
            'active' => 's.active',
            'name' => 'sl.name',
            'address' => 'sl.address1',
            'city' => 's.city',
            'postcode' => 's.postcode',
            'state' => 'state.name',
            'country' => 'cl.name',
            'phone' => 's.phone',
            'fax' => 's.fax',
        ];

        foreach ($filters as $filterName => $value) {
            // make sure filters are known, to avoid sql injection
            if (!array_key_exists($filterName, $filtersMap)) {
                continue;
            }

            $dbColumn = $filtersMap[$filterName];

            // apply strict filtering only for certain fields
            if ('id_store' === $filterName || 'active' === $filterName) {
                $qb->andWhere($dbColumn . ' = :' . $filterName)
                    ->setParameter($filterName, $value);

                continue;
            }

            // and wildcard for all other filters
            $qb->andWhere($dbColumn . ' LIKE :' . $filterName)
                ->setParameter($filterName, '%' . $value . '%');
        }
    }
}