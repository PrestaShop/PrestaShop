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

namespace PrestaShop\PrestaShop\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class ManufacturerQueryBuilder is responsible for building queries for manufacturers grid data.
 */
final class ManufacturerQueryBuilder extends AbstractDoctrineQueryBuilder
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
        $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        array $contextShopIds
    ) {
        parent::__construct($connection, $dbPrefix);

        $this->contextShopIds = $contextShopIds;
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $addressesQb = $this->connection->createQueryBuilder();
        $addressesQb->select('COUNT(a.`id_manufacturer`) AS `addresses_count`')
            ->from($this->dbPrefix . 'address', 'a')
            ->where('m.`id_manufacturer` = a.`id_manufacturer`')
            ->andWhere('a.`deleted` = 0')
            ->groupBy('a.`id_manufacturer`')
        ;

        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb
            ->select('m.`id_manufacturer`, m.`name`, m.`active`')
            ->addSelect('COUNT(p.`id_product`) AS `products_count`')
            ->addSelect('(' . $addressesQb->getSQL() . ') AS addresses_count')
            ->groupBy('m.`id_manufacturer`')
        ;

        $this->searchCriteriaApplicator
            ->applyPagination($searchCriteria, $qb)
            ->applySorting($searchCriteria, $qb)
        ;

        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria->getFilters());
        $qb->select('COUNT(DISTINCT m.`id_manufacturer`)');

        return $qb;
    }

    /**
     * Get generic query builder.
     *
     * @param array $filters
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(array $filters)
    {
        $allowedFilters = ['id_manufacturer', 'name', 'active'];

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'manufacturer', 'm')
            ->innerJoin(
                'm',
                $this->dbPrefix . 'manufacturer_shop',
                'ms',
                'ms.`id_manufacturer` = m.`id_manufacturer`'
            )
            ->leftJoin(
                'm',
                $this->dbPrefix . 'product',
                'p',
                'm.`id_manufacturer` = p.`id_manufacturer`'
            )
        ;

        foreach ($filters as $filterName => $value) {
            if (!in_array($filterName, $allowedFilters, true)) {
                continue;
            }

            if ('name' === $filterName) {
                $qb->andWhere('m.`name` LIKE :' . $filterName)
                    ->setParameter($filterName, '%' . $value . '%');
                continue;
            }
            $qb->andWhere('m.`' . $filterName . '` = :' . $filterName)
                ->setParameter($filterName, $value);
        }

        $qb->andWhere('ms.`id_shop` IN (:contextShopIds)');

        $qb->setParameter('contextShopIds', $this->contextShopIds, Connection::PARAM_INT_ARRAY);

        return $qb;
    }
}
