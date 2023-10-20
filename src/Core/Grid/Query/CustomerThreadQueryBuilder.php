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
 * Builds search and count query builders for customer thread grid.
 */
class CustomerThreadQueryBuilder extends AbstractDoctrineQueryBuilder
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
            ->select('ct.*, CONCAT(c.`firstname`," ",c.`lastname`) as customer')
            ->addSelect('cm.private, cl.name as contact, l.name as langName')
            ->addSelect('s.name as shopName')

            // we need to get only the latest message and its employee
            ->addSelect('(
				SELECT cm3.message
				FROM `' . _DB_PREFIX_ . 'customer_message` cm3
				WHERE cm3.`id_customer_thread` = ct.`id_customer_thread`
				ORDER BY cm3.`date_add` DESC LIMIT 1
			) as message')
            ->addSelect('(
				SELECT IFNULL(CONCAT(LEFT(e.`firstname`, 1),". ",e.`lastname`), "--")
				FROM `' . _DB_PREFIX_ . 'customer_message` cm2
				INNER JOIN ' . _DB_PREFIX_ . 'employee e
					ON e.`id_employee` = cm2.`id_employee`
				WHERE cm2.id_employee > 0
					AND cm2.`id_customer_thread` = ct.`id_customer_thread`
				ORDER BY cm2.`date_add` DESC LIMIT 1
			) as employee')

            ->addOrderBy('cm.date_add', 'DESC')
            ->groupBy('ct.id_customer_thread');

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
        return $this->getQueryBuilder($searchCriteria)->select('COUNT(DISTINCT ct.id_customer_thread)');
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'customer_thread', 'ct')
            ->leftJoin(
                'ct',
                $this->dbPrefix . 'customer',
                'c',
                'ct.id_customer = c.id_customer'
            )
            ->innerJoin(
                'ct',
                $this->dbPrefix . 'lang',
                'l',
                'ct.id_lang = l.id_lang'
            )
            ->innerJoin(
                'ct',
                $this->dbPrefix . 'customer_message',
                'cm',
                'ct.id_customer_thread = cm.id_customer_thread'
            )
            ->leftJoin(
                'cm',
                $this->dbPrefix . 'employee',
                'e',
                'cm.id_employee = e.id_employee'
            )
            ->leftJoin(
                'ct',
                $this->dbPrefix . 'contact_lang',
                'cl',
                'ct.id_contact = cl.id_contact AND ct.id_lang = cl.id_lang'
            )
            ->innerJoin(
                'ct',
                $this->dbPrefix . 'shop',
                's',
                'ct.id_shop = s.id_shop'
            )
            ->where('ct.id_shop IN (:contextShopIds)')
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
            'id_customer_thread',
            'customer',
            'email',
            'contact',
            'langName',
            'status',
            'employee',
            'message',
            'private',
            'upd_at',
            'shopName',
        ];

        foreach ($criteria->getFilters() as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if (in_array($filterName, ['message', 'private'])) {
                $builder->andWhere('cm.' . $filterName . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'contact') {
                $builder->andWhere('cl.id_contact' . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'shopName') {
                $builder->andWhere('s.id_shop' . ' = :' . $filterName);
                $builder->setParameter($filterName, $filterValue);
                continue;
            }

            if ($filterName === 'langName') {
                $builder->andWhere('l.name' . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'customer') {
                $builder->andWhere('CONCAT(c.`firstname`," ",c.`lastname`)' . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ($filterName === 'employee') {
                $builder->andWhere('CONCAT(LEFT(e.`firstname`, 1),". ",e.`lastname`)' . ' LIKE :' . $filterName);
                $builder->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            $builder->andWhere('ct.' . $filterName . ' LIKE :' . $filterName);
            $builder->setParameter($filterName, '%' . $filterValue . '%');
        }
    }
}
