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
 * Provides SQL for carriers listing.
 */
final class CarrierQueryBuilder extends AbstractDoctrineQueryBuilder
{
    private const ALLOWED_FILTERS = [
        'id_carrier',
        'name',
        'delay',
        'active',
        'is_free',
        'position',
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
        $qb = $this->getCarrierQueryBuilder($searchCriteria)
            ->select('c.id_carrier, c.name, cl.delay, c.active, c.is_free, c.position')
            ->groupBy('c.id_carrier');

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb);

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        return $this->getCarrierQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT c.id_carrier)');
    }

    private function getCarrierQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'carrier', 'c')
            ->innerJoin('c', $this->dbPrefix . 'carrier_lang', 'cl', 'c.id_carrier = cl.id_carrier')
            ->innerJoin('c', $this->dbPrefix . 'carrier_shop', 'cs', 'c.id_carrier = cs.id_carrier')
            ->andWhere('cl.id_lang = :contextIdLang')
            ->andWhere('cs.id_shop IN (:contextShopIds)')
            ->andWhere('cl.id_shop IN (:contextShopIds)')
            ->andWhere('c.deleted = 0')
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
                $qb->andWhere('c.name LIKE :name');
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ($filterName === 'delay') {
                $qb->andWhere('cl.delay LIKE :delay');
                $qb->setParameter($filterName, '%' . $filterValue . '%');

                continue;
            }

            if ($filterName === 'position') {
                // When filtering by position,
                // value must be decreased by 1,
                // since position value in database starts at 0,
                // but for user display positions are increased by 1.
                if (is_numeric($filterValue)) {
                    --$filterValue;
                } else {
                    $filterValue = null;
                }

                $qb->andWhere('c.position = :position');
                $qb->setParameter($filterName, $filterValue);

                continue;
            }

            $qb->andWhere('c.' . $filterName . ' = :' . $filterName);
            $qb->setParameter($filterName, $filterValue);
        }
    }
}
