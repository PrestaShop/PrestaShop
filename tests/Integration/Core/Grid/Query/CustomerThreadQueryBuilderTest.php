<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Query\CustomerThreadQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CustomerThreadQueryBuilderTest extends KernelTestCase
{
    private const CONTEXT_LANG_ID = 1;
    private const CONTEXT_SHOP_ID = 1;

    private Connection $connection;
    private string $dbPrefix;
    private CustomerThreadQueryBuilder $queryBuilder;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->queryBuilder = new CustomerThreadQueryBuilder(
            $this->connection,
            $this->dbPrefix,
            new DoctrineSearchCriteriaApplicator(),
            [self::CONTEXT_SHOP_ID]
        );
    }

    /**
     * The Employee column is rendered abbreviated ("P. Mummy") and the filter used to match that
     * same expression, so searching by an employee's real first name returned nothing - while the
     * customer filter directly above it in the same method already matched the full name.
     */
    public function testEmployeeFilterMatchesTheFullFirstName(): void
    {
        $searchedEmployee = $this->insertEmployee('Zzjonathan', 'Zzsmith');
        $otherEmployee = $this->insertEmployee('Zzmarianne', 'Zzjones');
        $searchedThread = $this->insertThread();
        $otherThread = $this->insertThread();
        $searchedMessage = $this->insertMessage($searchedThread, $searchedEmployee);
        $otherMessage = $this->insertMessage($otherThread, $otherEmployee);

        try {
            $this->assertSame([$searchedThread], $this->fetchThreadIds('Zzjonathan'));
            $this->assertSame([$otherThread], $this->fetchThreadIds('Zzmarianne'));
            // Searching by last name worked before the fix and must keep working.
            $this->assertSame([$searchedThread], $this->fetchThreadIds('Zzsmith'));
        } finally {
            $this->deleteFrom('customer_message', 'id_customer_message', [$searchedMessage, $otherMessage]);
            $this->deleteFrom('customer_thread', 'id_customer_thread', [$searchedThread, $otherThread]);
            $this->deleteFrom('employee', 'id_employee', [$searchedEmployee, $otherEmployee]);
        }
    }

    /**
     * @return int[]
     */
    private function fetchThreadIds(string $search): array
    {
        $rows = $this->queryBuilder
            ->getSearchQueryBuilder($this->createSearchCriteria(['employee' => $search]))
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map('intval', array_column($rows, 'id_customer_thread'));
    }

    private function insertEmployee(string $firstname, string $lastname): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'employee
                (id_profile, id_lang, lastname, firstname, email, passwd, active)
             VALUES (1, :lang, :lastname, :firstname, :email, :passwd, 1)',
            [
                'lang' => self::CONTEXT_LANG_ID,
                'lastname' => $lastname,
                'firstname' => $firstname,
                'email' => strtolower($firstname . '.' . $lastname) . '@thread-grid-test.invalid',
                'passwd' => 'thread-grid-test',
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function insertThread(): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'customer_thread
                (id_shop, id_lang, id_contact, id_customer, id_order, id_product, status, email,
                 token, date_add, date_upd)
             VALUES (:shop, :lang, 0, 0, 0, 0, :status, :email, :token, NOW(), NOW())',
            [
                'shop' => self::CONTEXT_SHOP_ID,
                'lang' => self::CONTEXT_LANG_ID,
                'status' => 'open',
                'email' => 'thread@thread-grid-test.invalid',
                'token' => 'zzgridtest',
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    private function insertMessage(int $threadId, int $employeeId): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'customer_message
                (id_customer_thread, id_employee, id_product, message, private, `read`, date_add, date_upd)
             VALUES (:thread, :employee, 0, :message, 0, 0, NOW(), NOW())',
            [
                'thread' => $threadId,
                'employee' => $employeeId,
                'message' => 'Reply from the grid test employee',
            ]
        );

        return (int) $this->connection->lastInsertId();
    }

    /**
     * @param int[] $ids
     */
    private function deleteFrom(string $table, string $idColumn, array $ids): void
    {
        $this->connection->executeStatement(
            'DELETE FROM ' . $this->dbPrefix . $table . ' WHERE ' . $idColumn . ' IN (:ids)',
            ['ids' => $ids],
            ['ids' => Connection::PARAM_INT_ARRAY]
        );
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
                return 'id_customer_thread';
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
