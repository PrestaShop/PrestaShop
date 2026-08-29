<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\OrderQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderQueryBuilderTest extends KernelTestCase
{
    private const CONTEXT_LANG_ID = 1;
    private const CONTEXT_SHOP_ID = 1;
    private const DEMO_CUSTOMER_ID = 1;
    private const FILTERED_ORDER_STATE_ID = 2;
    private const OTHER_ORDER_STATE_ID = 3;

    private Connection $connection;
    private string $dbPrefix;
    private OrderQueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->queryBuilder = new OrderQueryBuilder(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            self::CONTEXT_LANG_ID,
            [self::CONTEXT_SHOP_ID]
        );
    }

    /**
     * The orders grid can be filtered by country, which lives on a joined table. When the newest
     * orders belong to another country, the filter must still return the matching orders whatever
     * the page size. Resolving the page of order ids before applying the filter would drop them —
     * this guards the id-first pagination in getSearchQueryBuilder() against that regression.
     */
    public function testCountryFilterIsNotTruncatedByPagination(): void
    {
        $addressInCountryA = $this->insertAddress(1);
        $addressInCountryB = $this->insertAddress(2);

        // Two orders in country B, then three NEWER orders (higher id_order) in country A.
        $olderBOrder = $this->insertOrder($addressInCountryB);
        $newerBOrder = $this->insertOrder($addressInCountryB);
        $newestAOrders = [
            $this->insertOrder($addressInCountryA),
            $this->insertOrder($addressInCountryA),
            $this->insertOrder($addressInCountryA),
        ];
        $createdOrderIds = array_merge([$olderBOrder, $newerBOrder], $newestAOrders);

        try {
            // Page size 2 while the two newest orders are in country A: a query that limited the
            // ids before filtering would return nothing for country B.
            $criteria = $this->createSearchCriteria(['country_name' => 2], 'id_order', 'DESC', 2, 0);
            $ids = $this->fetchOrderIds($criteria);

            $this->assertSame([$newerBOrder, $olderBOrder], $ids);
        } finally {
            $this->deleteOrders($createdOrderIds);
            $this->deleteAddresses([$addressInCountryA, $addressInCountryB]);
        }
    }

    /**
     * The grid is also filtered by order status, which is reached through a LEFT JOIN on
     * order_state rather than the country filter's INNER JOIN chain. Same regression, different
     * join type: once the newest orders carry another status, resolving the page of ids before
     * applying the filter returns nothing for the status actually asked for.
     */
    public function testOrderStatusFilterIsNotTruncatedByPagination(): void
    {
        $address = $this->insertAddress(1);

        // Two orders in the filtered status, then three NEWER ones (higher id_order) in another.
        $olderMatch = $this->insertOrder($address, self::FILTERED_ORDER_STATE_ID);
        $newerMatch = $this->insertOrder($address, self::FILTERED_ORDER_STATE_ID);
        $newestOthers = [
            $this->insertOrder($address, self::OTHER_ORDER_STATE_ID),
            $this->insertOrder($address, self::OTHER_ORDER_STATE_ID),
            $this->insertOrder($address, self::OTHER_ORDER_STATE_ID),
        ];
        $createdOrderIds = array_merge([$olderMatch, $newerMatch], $newestOthers);

        try {
            $criteria = $this->createSearchCriteria(
                ['osname' => self::FILTERED_ORDER_STATE_ID],
                'id_order',
                'DESC',
                2,
                0
            );
            $ids = $this->fetchOrderIds($criteria);

            $this->assertSame([$newerMatch, $olderMatch], $ids);
        } finally {
            $this->deleteOrders($createdOrderIds);
            $this->deleteAddresses([$address]);
        }
    }

    /**
     * Baseline: the default grid returns the newest orders, ordered by id, honouring the page size.
     */
    public function testDefaultPageReturnsNewestOrdersInDescendingOrder(): void
    {
        $address = $this->insertAddress(1);
        $createdOrderIds = [
            $this->insertOrder($address),
            $this->insertOrder($address),
            $this->insertOrder($address),
        ];

        try {
            $criteria = $this->createSearchCriteria([], 'id_order', 'DESC', 2, 0);
            $ids = $this->fetchOrderIds($criteria);

            $this->assertCount(2, $ids);
            $this->assertSame([$createdOrderIds[2], $createdOrderIds[1]], $ids);
        } finally {
            $this->deleteOrders($createdOrderIds);
            $this->deleteAddresses([$address]);
        }
    }

    /**
     * @return int[]
     */
    private function fetchOrderIds(SearchCriteriaInterface $criteria): array
    {
        $rows = $this->queryBuilder
            ->getSearchQueryBuilder($criteria)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map('intval', array_column($rows, 'id_order'));
    }

    private function insertAddress(int $countryId): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'address
                (id_customer, id_country, alias, lastname, firstname, address1, city, date_add, date_upd)
             VALUES (:customer, :country, :alias, :lastname, :firstname, :address, :city, NOW(), NOW())',
            [
                'customer' => self::DEMO_CUSTOMER_ID,
                'country' => $countryId,
                'alias' => 'order-grid-test',
                'lastname' => 'Test',
                'firstname' => 'Test',
                'address' => 'Test street',
                'city' => 'Test city',
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function insertOrder(int $addressId, int $orderStateId = 1): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'orders
                (id_address_delivery, id_address_invoice, id_cart, id_currency, id_lang, id_customer,
                 id_carrier, current_state, payment, reference, id_shop, id_shop_group,
                 date_add, date_upd, delivery_date, invoice_date, valid)
             VALUES (:address, :address, 0, :currency, :lang, :customer,
                 0, :orderState, :payment, :reference, :shop, :shopGroup,
                 NOW(), NOW(), NOW(), NOW(), 1)',
            [
                'address' => $addressId,
                'orderState' => $orderStateId,
                'currency' => 1,
                'lang' => self::CONTEXT_LANG_ID,
                'customer' => self::DEMO_CUSTOMER_ID,
                'payment' => 'Test payment',
                'reference' => 'ORDERGRIDTEST',
                'shop' => self::CONTEXT_SHOP_ID,
                'shopGroup' => 1,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @param int[] $orderIds
     */
    private function deleteOrders(array $orderIds): void
    {
        if (empty($orderIds)) {
            return;
        }

        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . 'orders WHERE id_order IN (:ids)',
            ['ids' => $orderIds],
            ['ids' => Connection::PARAM_INT_ARRAY]
        );
    }

    /**
     * @param int[] $addressIds
     */
    private function deleteAddresses(array $addressIds): void
    {
        if (empty($addressIds)) {
            return;
        }

        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . 'address WHERE id_address IN (:ids)',
            ['ids' => $addressIds],
            ['ids' => Connection::PARAM_INT_ARRAY]
        );
    }

    /**
     * @param array<string, mixed> $filters
     */
    private function createSearchCriteria(array $filters, string $orderBy, string $orderWay, int $limit, int $offset): SearchCriteriaInterface
    {
        return new class($filters, $orderBy, $orderWay, $limit, $offset) implements SearchCriteriaInterface {
            /**
             * @param array<string, mixed> $filters
             */
            public function __construct(
                private array $filters,
                private string $orderBy,
                private string $orderWay,
                private int $limit,
                private int $offset
            ) {
            }

            public function getOrderBy()
            {
                return $this->orderBy;
            }

            public function getOrderWay()
            {
                return $this->orderWay;
            }

            public function getOffset()
            {
                return $this->offset;
            }

            public function getLimit()
            {
                return $this->limit;
            }

            public function getFilters()
            {
                return $this->filters;
            }
        };
    }
}
