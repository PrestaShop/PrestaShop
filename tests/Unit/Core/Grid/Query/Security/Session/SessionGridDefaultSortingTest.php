<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query\Security\Session;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\Security\Session\CustomerQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\Security\Session\EmployeeQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\CustomerFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\Security\Session\EmployeeFilters;

/**
 * Sessions are an append-only log: the most recent rows are the useful ones, so both
 * session grids must land on the newest session first, like the Logs, E-mail logs and
 * Orders grids already do.
 */
class SessionGridDefaultSortingTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    public function testCustomerSessionGridIsSortedNewestFirstByDefault(): void
    {
        $queryBuilder = new CustomerQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            new DoctrineSearchCriteriaApplicator()
        );

        $qb = $queryBuilder->getSearchQueryBuilder(new CustomerFilters(CustomerFilters::getDefaults()));

        $this->assertStringContainsString('ORDER BY id_customer_session desc', $qb->getSQL());
    }

    public function testEmployeeSessionGridIsSortedNewestFirstByDefault(): void
    {
        $queryBuilder = new EmployeeQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            new DoctrineSearchCriteriaApplicator()
        );

        $qb = $queryBuilder->getSearchQueryBuilder(new EmployeeFilters(EmployeeFilters::getDefaults()));

        $this->assertStringContainsString('ORDER BY id_employee_session desc', $qb->getSQL());
    }

    private function getMockConnection(): Connection
    {
        $mock = $this->createMock(Connection::class);
        $mock->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $mock->method('createQueryBuilder')->willReturnCallback(function () use (&$mock): QueryBuilder {
            return new QueryBuilder($mock);
        });

        return $mock;
    }
}
