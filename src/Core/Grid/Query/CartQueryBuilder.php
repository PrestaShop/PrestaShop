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
use PrestaShop\PrestaShop\Core\Domain\Cart\CartStatus;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Builds search & count queries for cart grid.
 */
final class CartQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * @var DoctrineSearchCriteriaApplicatorInterface
     */
    private $searchCriteriaApplicator;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /** @var int */
    private const CUSTOMER_ONLINE_TIME = 1800; // 30 min

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        DoctrineSearchCriteriaApplicatorInterface $searchCriteriaApplicator,
        MultistoreContextCheckerInterface $multistoreContextChecker,
    ) {
        parent::__construct($connection, $dbPrefix);
        $this->searchCriteriaApplicator = $searchCriteriaApplicator;
        $this->multistoreContextChecker = $multistoreContextChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getQueryBuilder($searchCriteria);

        $qb
            ->select('c.`id_cart`')
            ->addSelect('c.`date_add`')
            ->addSelect('ca.`name` AS carrier_name')
            ->addSelect('o.`id_order` AS id_order')
            ->addSelect('CONCAT(LEFT(cu.firstname, 1), ". ", cu.lastname) AS customer_name')
            ->addSelect('co.`id_guest` AS customer_online')
            ->addSelect('s.`name` AS shop_name')
            ->addSelect('o.`total_products` AS cart_total')
            ->addSelect($this->getCartStatusQuery() . ' AS status')
            ->setParameter('current_date', date('Y-m-d H:i:00', time()))
            ->setParameter('cart_expiration_time', CartStatus::ABANDONED_CART_EXPIRATION_TIME)
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
        return $this->getQueryBuilder($searchCriteria)
            ->select('COUNT(DISTINCT c.`id_cart`)');
    }

    /**
     * Format cart status query.
     *
     * @return string
     */
    private function getCartStatusQuery(): string
    {
        return '(IF
           (
                IFNULL(o.id_order, "not_ordered") = "not_ordered",
                IF (TIME_TO_SEC(TIMEDIFF(:current_date, c.date_add)) > :cart_expiration_time, "abandoned_cart", "not_ordered"),
                "ordered"
           )
       )';
    }

    /**
     * Gets query builder with the common sql used for displaying carts list and applying filter actions.
     *
     * @param SearchCriteriaInterface $searchCriteria
     *
     * @return QueryBuilder
     *
     * @throws InvalidArgumentException
     */
    private function getQueryBuilder(SearchCriteriaInterface $searchCriteria): QueryBuilder
    {
        if (!$searchCriteria instanceof ShopSearchCriteriaInterface) {
            throw new InvalidArgumentException('Invalid search criteria type');
        }

        $filters = $searchCriteria->getFilters();

        $qbOnline = $this->connection
            ->createQueryBuilder()
            ->select('DISTINCT co.`id_guest`')
            ->from($this->dbPrefix . 'connections', 'co')
            ->where('TIME_TO_SEC(TIMEDIFF(\'' . pSQL(date('Y-m-d H:i:00', time())) . '\', `date_add`)) < ' . self::CUSTOMER_ONLINE_TIME);

        $qb = $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'cart', 'c');

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'carrier',
            'ca',
            'c.`id_carrier` = ca.`id_carrier`'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'orders',
            'o',
            'c.`id_cart` = o.`id_cart`'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'customer',
            'cu',
            'c.`id_customer` = cu.`id_customer`'
        );

        $qb->leftJoin(
            'c',
            '(' . $qbOnline->getSQL() . ')',
            'co',
            'co.`id_guest` = c.`id_guest`'
        );

        $qb->leftJoin(
            'c',
            $this->dbPrefix . 'shop',
            's',
            'c.`id_shop` = s.`id_shop`'
        );

        $shopConstraint = $searchCriteria->getShopConstraint();
        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $qb->andWhere('s.`id_shop` = :shopId');
            $qb->setParameter('shopId', $shopConstraint->getShopId()->getValue());
        } elseif ($this->multistoreContextChecker->isGroupShopContext()) {
            $qb->andWhere('s.`id_shop_group` = :shopGroupId');
            $qb->setParameter('shopGroupId', $shopConstraint->getShopGroupId()->getValue());
        }

        $this->applyFilters($qb, $filters);

        return $qb;
    }

    /**
     * Apply filters to carts query builder
     *
     * @param QueryBuilder $qb
     * @param array<string, mixed> $filters
     *
     * @return void
     */
    private function applyFilters(QueryBuilder $qb, array $filters): void
    {
        $allowedFilters = [
            'id_cart',
            'id_order',
            'status',
            'customer_name',
            'carrier_name',
            'date_add',
            'customer_online',
        ];

        foreach ($filters as $filterName => $filterValue) {
            if (!in_array($filterName, $allowedFilters)) {
                continue;
            }

            if ('id_cart' === $filterName) {
                $qb->andWhere('c.id_cart = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ('id_order' === $filterName) {
                $qb->andWhere('o.id_order = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                continue;
            }

            if ('status' === $filterName) {
                $qb->andWhere($this->getCartStatusQuery() . ' = :' . $filterName);
                $qb->setParameter($filterName, $filterValue);
                $qb->setParameter('current_date', date('Y-m-d H:i:00', time()));
                $qb->setParameter('cart_expiration_time', CartStatus::ABANDONED_CART_EXPIRATION_TIME);
                continue;
            }

            if ('customer_name' === $filterName) {
                $qb->andWhere('cu.firstname LIKE :' . $filterName . ' OR cu.lastname LIKE :' . $filterName . ' OR CONCAT(LEFT(cu.firstname, 1), ". ", cu.lastname) LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ('carrier_name' === $filterName) {
                $qb->andWhere('ca.name LIKE :' . $filterName);
                $qb->setParameter($filterName, '%' . $filterValue . '%');
                continue;
            }

            if ('date_add' === $filterName) {
                if (isset($filterValue['from'])) {
                    $qb->andWhere('c.date_add >= :date_from');
                    $qb->setParameter('date_from', sprintf('%s 0:0:0', $filterValue['from']));
                }

                if (isset($filterValue['to'])) {
                    $qb->andWhere('c.date_add <= :date_to');
                    $qb->setParameter('date_to', sprintf('%s 23:59:59', $filterValue['to']));
                }

                continue;
            }

            if ('customer_online' === $filterName) {
                if ($filterValue) {
                    $qb->andWhere('co.id_guest > 0');
                } else {
                    $qb->andWhere('co.id_guest is null');
                }
                continue;
            }

            $qb->andWhere('c.' . $filterName . ' = :' . $filterName);
            $qb->setParameter($filterName, $filterValue);
        }
    }
}
