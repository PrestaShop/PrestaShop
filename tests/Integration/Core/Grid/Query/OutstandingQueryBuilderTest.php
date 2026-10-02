<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\OutstandingQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OutstandingQueryBuilderTest extends KernelTestCase
{
    private const CONTEXT_LANG_ID = 1;
    private const CONTEXT_SHOP_ID = 1;

    private Connection $connection;
    private string $dbPrefix;
    private OutstandingQueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->queryBuilder = new OutstandingQueryBuilder(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            self::CONTEXT_LANG_ID,
            [self::CONTEXT_SHOP_ID]
        );
    }

    /**
     * The customer column is rendered abbreviated ("J. Doe") and the filter used to match that same
     * expression, so searching the outstanding grid for a customer's real first name returned
     * nothing. The filter has to match the full name while the column keeps its display form.
     */
    public function testCustomerFilterMatchesTheFullFirstName(): void
    {
        $searched = $this->insertCustomer('Zzjonathan', 'Zzsmith');
        $other = $this->insertCustomer('Zzmarianne', 'Zzjones');
        $searchedOrder = $this->insertOrder($searched);
        $otherOrder = $this->insertOrder($other);
        $searchedInvoice = $this->insertInvoice($searchedOrder);
        $otherInvoice = $this->insertInvoice($otherOrder);

        try {
            $this->assertSame([$searchedInvoice], $this->fetchInvoiceIds('Zzjonathan'));
            $this->assertSame([$otherInvoice], $this->fetchInvoiceIds('Zzmarianne'));
            // Searching by last name worked before the fix and must keep working.
            $this->assertSame([$searchedInvoice], $this->fetchInvoiceIds('Zzsmith'));
        } finally {
            $this->connection->executeStatement(
                'DELETE FROM ' . $this->dbPrefix . 'order_invoice WHERE id_order_invoice IN (:ids)',
                ['ids' => [$searchedInvoice, $otherInvoice]],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
            $this->connection->executeStatement(
                'DELETE FROM ' . $this->dbPrefix . 'orders WHERE id_order IN (:ids)',
                ['ids' => [$searchedOrder, $otherOrder]],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
            $this->connection->executeStatement(
                'DELETE FROM ' . $this->dbPrefix . 'customer WHERE id_customer IN (:ids)',
                ['ids' => [$searched, $other]],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
        }
    }

    /**
     * @return int[]
     */
    private function fetchInvoiceIds(string $search): array
    {
        $rows = $this->queryBuilder
            ->getSearchQueryBuilder($this->createSearchCriteria(['customer' => $search]))
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map('intval', array_column($rows, 'id_invoice'));
    }

    private function insertCustomer(string $firstname, string $lastname): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'customer
                (id_shop_group, id_shop, id_gender, id_default_group, id_lang, firstname, lastname,
                 email, passwd, active, date_add, date_upd)
             VALUES (1, :shop, 0, 3, :lang, :firstname, :lastname, :email, :passwd, 1, NOW(), NOW())',
            [
                'shop' => self::CONTEXT_SHOP_ID,
                'lang' => self::CONTEXT_LANG_ID,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => strtolower($firstname . '.' . $lastname) . '@outstanding-grid-test.invalid',
                'passwd' => 'outstanding-grid-test',
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function insertOrder(int $customerId): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'orders
                (id_address_delivery, id_address_invoice, id_cart, id_currency, id_lang, id_customer,
                 id_carrier, current_state, payment, reference, id_shop, id_shop_group,
                 date_add, date_upd, delivery_date, invoice_date, valid)
             VALUES (0, 0, 0, 1, :lang, :customer, 0, 1, :payment, :reference, :shop, 1,
                 NOW(), NOW(), NOW(), NOW(), 1)',
            [
                'lang' => self::CONTEXT_LANG_ID,
                'customer' => $customerId,
                'payment' => 'Test payment',
                'reference' => 'OUTSTANDINGTEST',
                'shop' => self::CONTEXT_SHOP_ID,
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function insertInvoice(int $orderId): int
    {
        // The grid only lists invoices with a number, hence `number > 0` in the base query.
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'order_invoice
                (id_order, number, delivery_number, shipping_tax_computation_method, date_add)
             VALUES (:order, 1, 0, 0, NOW())',
            ['order' => $orderId]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function createSearchCriteria(array $filters): SearchCriteriaInterface
    {
        return new class($filters) implements SearchCriteriaInterface {
            /**
             * @param array<string, mixed> $filters
             */
            public function __construct(private array $filters)
            {
            }

            public function getOrderBy()
            {
                return 'id_invoice';
            }

            public function getOrderWay()
            {
                return 'DESC';
            }

            public function getOffset()
            {
                return 0;
            }

            public function getLimit()
            {
                return 50;
            }

            public function getFilters()
            {
                return $this->filters;
            }
        };
    }
}
