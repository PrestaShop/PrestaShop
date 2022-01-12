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
 * Provides SQL for stores listing.
 */
final class StoreQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ALLOWED_FILTERS = [
        'id_store',
        'name',
        'address1',
        'city',
        'postcode',
        'id_state',
        'id_country',
        'phone',
        'fax',
        'active',
    ];

    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var string
     */
    private $contextIdLang;

    /**
     * @var int[]
     */
    private $contextShopIds;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param string $contextIdLang
     * @param array $contextShopIds
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        string $contextIdLang,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->contextIdLang = $contextIdLang;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getStoreQueryBuilder($searchCriteria)
            ->select('s.id_store, sl.name, sl.address1, s.city, s.phone, s.fax, s.active, s.postcode')
            ->addSelect('cl.`name` as country_name')
            ->addSelect('st.`name` as state_name')
            ->groupBy('s.id_store');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getStoreQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT s.id_store)');
    }

    private function getStoreQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'store', 's')
            ->innerJoin('s', $this->dbPrefix . 'store_lang', 'sl', 's.id_store = sl.id_store')
            ->innerJoin('s', $this->dbPrefix . 'store_shop', 'sc', 's.id_store = sc.id_store')
            ->leftJoin('s', $this->dbPrefix . 'country', 'c', 's.`id_country` = c.`id_country`')
            ->leftJoin('s', $this->dbPrefix . 'country_lang', 'cl', 'c.`id_country` = cl.`id_country` AND cl.`id_lang` = :contextIdLang')
            ->leftJoin('s', $this->dbPrefix . 'state', 'st', 's.`id_state` = st.`id_state`')
            ->andWhere('sl.id_lang = :contextIdLang')
            ->andWhere('sc.id_shop IN (:contextShopIds)')
            ->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('contextIdLang', $this->contextIdLang);

        $this->applyFilters($qb, $searchCriteria->getFilters());

        return $qb;
    }

    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, self::ALLOWED_FILTERS)) {
                continue;
            }

            if ($filterName === 'name') {
                $qb->andWhere('s.name LIKE :name');
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ($filterName === 'address1') {
                $qb->andWhere('sl.address1 LIKE :address1');
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }
            
            if ($filterName === 'city') {
                $qb->andWhere('s.city LIKE :city');
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }
            
            if ($filterName === 'id_state') {
                $qb->andWhere('st.id_state = :'.$filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }
            
            if ($filterName === 'id_country') {
                $qb->andWhere('cl.id_country = :'.$filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }
            
            if ($filterName === 'phone') {
                $qb->andWhere('s.phone LIKE :phone');
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }
            
            if ($filterName === 'fax') {
                $qb->andWhere('s.fax LIKE :fax');
                $qb->setParameter($filterName, '%' . $filterValue . '%');
            
                continue;
            }

            $qb->andWhere('s.' . $filterName . ' = :' . $filterName);
            $qb->setParameter($filterName, $filterValue);
        }
    }
}
