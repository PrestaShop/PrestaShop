<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\CustomerQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Search\Filters\CustomerFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Guards the id-first pagination of the customers grid: the query must keep returning the same
 * customers, in the same order, with the same computed columns, and must not truncate the page.
 */
class CustomerQueryBuilderTest extends KernelTestCase
{
    private const PREFIX = 'ps_';
    private const EMAIL_MARKER = 'qbtestpr_';

    /** @var Connection */
    private $connection;

    /** @var CustomerQueryBuilder */
    private $queryBuilder;

    /** @var array<string, int> label => id_customer */
    private $customerIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->queryBuilder = new CustomerQueryBuilder(
            $this->connection,
            self::PREFIX,
            new DoctrineSearchCriteriaApplicator(),
            1,
            [1]
        );
        $this->seedCustomers();
    }

    protected function tearDown(): void
    {
        $ids = array_values($this->customerIds);
        if (!empty($ids)) {
            $this->connection->executeStatement(
                'DELETE FROM ' . self::PREFIX . 'orders WHERE id_customer IN (' . implode(',', $ids) . ')'
            );
            $this->connection->executeStatement(
                'DELETE FROM ' . self::PREFIX . 'customer WHERE id_customer IN (' . implode(',', $ids) . ')'
            );
        }
        parent::tearDown();
    }

    public function testItReturnsSeededCustomersNewestFirstWithTheirTotalSpent(): void
    {
        $rows = $this->search(['orderBy' => 'date_add', 'sortOrder' => 'DESC']);

        // Delta and Bravo have no valid orders; Alpha spent 150, Charlie spent 200.
        $this->assertSame(
            [
                $this->customerIds['delta'],
                $this->customerIds['charlie'],
                $this->customerIds['bravo'],
                $this->customerIds['alpha'],
            ],
            array_map(static fn (array $r): int => (int) $r['id_customer'], $rows)
        );

        $spentById = [];
        foreach ($rows as $r) {
            $spentById[(int) $r['id_customer']] = null === $r['total_spent'] ? null : (float) $r['total_spent'];
        }
        $this->assertSame(150.0, $spentById[$this->customerIds['alpha']]);
        $this->assertSame(200.0, $spentById[$this->customerIds['charlie']]);
        $this->assertNull($spentById[$this->customerIds['bravo']]);
    }

    public function testPaginationDoesNotTruncateResults(): void
    {
        $page1 = $this->search(['orderBy' => 'date_add', 'sortOrder' => 'DESC', 'limit' => 2, 'offset' => 0]);
        $page2 = $this->search(['orderBy' => 'date_add', 'sortOrder' => 'DESC', 'limit' => 2, 'offset' => 2]);

        $this->assertCount(2, $page1);
        $this->assertCount(2, $page2);

        $seen = array_map(static fn (array $r): int => (int) $r['id_customer'], array_merge($page1, $page2));
        sort($seen);
        $expected = array_values($this->customerIds);
        sort($expected);
        $this->assertSame($expected, $seen);
    }

    public function testItCanSortByTheComputedTotalSpentColumn(): void
    {
        $rows = $this->search(['orderBy' => 'total_spent', 'sortOrder' => 'DESC']);
        $ordered = array_map(static fn (array $r): int => (int) $r['id_customer'], $rows);

        // Charlie (200) before Alpha (150); both before the customers with no spend.
        $this->assertSame($this->customerIds['charlie'], $ordered[0]);
        $this->assertSame($this->customerIds['alpha'], $ordered[1]);
    }

    /**
     * @param array<string, mixed> $criteria
     *
     * @return array<int, array<string, mixed>>
     */
    private function search(array $criteria): array
    {
        $criteria += ['limit' => 50, 'offset' => 0, 'filters' => ['email' => self::EMAIL_MARKER]];

        return $this->queryBuilder
            ->getSearchQueryBuilder(new CustomerFilters($criteria))
            ->executeQuery()
            ->fetchAllAssociative();
    }

    private function seedCustomers(): void
    {
        $customers = [
            'alpha' => '2020-01-01 00:00:00',
            'bravo' => '2020-06-01 00:00:00',
            'charlie' => '2021-01-01 00:00:00',
            'delta' => '2021-06-01 00:00:00',
        ];
        foreach ($customers as $label => $dateAdd) {
            $this->connection->insert(self::PREFIX . 'customer', [
                'id_shop' => 1,
                'id_shop_group' => 1,
                'id_gender' => 1,
                'id_default_group' => 3,
                'id_lang' => 1,
                'firstname' => ucfirst($label),
                'lastname' => 'QBTESTPR',
                'email' => self::EMAIL_MARKER . $label . '@example.com',
                'passwd' => 'x',
                'date_add' => $dateAdd,
                'date_upd' => $dateAdd,
                'newsletter' => 0,
                'optin' => 0,
                'active' => 1,
                'deleted' => 0,
                'secure_key' => md5($label),
            ]);
            $this->customerIds[$label] = (int) $this->connection->lastInsertId();
        }

        // Alpha: 100 + 50 = 150 (valid). Charlie: 200 (valid). One invalid order must be ignored.
        $this->seedValidOrder($this->customerIds['alpha'], 100);
        $this->seedValidOrder($this->customerIds['alpha'], 50);
        $this->seedValidOrder($this->customerIds['charlie'], 200);
        $this->seedOrder($this->customerIds['bravo'], 999, false);
    }

    private function seedValidOrder(int $customerId, float $total): void
    {
        $this->seedOrder($customerId, $total, true);
    }

    private function seedOrder(int $customerId, float $total, bool $valid): void
    {
        $this->connection->insert(self::PREFIX . 'orders', [
            'id_customer' => $customerId,
            'id_shop' => 1,
            'id_shop_group' => 1,
            'id_cart' => 0,
            'id_currency' => 1,
            'id_lang' => 1,
            'id_carrier' => 1,
            'id_address_delivery' => 0,
            'id_address_invoice' => 0,
            'current_state' => 2,
            'valid' => $valid ? 1 : 0,
            'total_paid_tax_incl' => $total,
            'total_paid' => $total,
            'total_paid_real' => $total,
            'conversion_rate' => 1,
            'payment' => 'Test',
            'module' => 'test',
            'secure_key' => 'k',
            'invoice_date' => '2020-01-01 00:00:00',
            'delivery_date' => '2020-01-01 00:00:00',
            'date_add' => '2020-01-01 00:00:00',
            'date_upd' => '2020-01-01 00:00:00',
        ]);
    }
}
