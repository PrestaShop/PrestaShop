<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicator;
use PrestaShop\PrestaShop\Core\Grid\Query\LogQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\LogsFilters;

class LogQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    public function testCountQueryOnlyReadsTheLogTable(): void
    {
        $qb = $this->buildQueryBuilder()->getCountQueryBuilder($this->getFilters([]));

        $this->assertSame('SELECT COUNT(lg.id_log) FROM ps_log lg', $qb->getSQL());
    }

    public function testCountQueryAppliesFilters(): void
    {
        $qb = $this->buildQueryBuilder()->getCountQueryBuilder($this->getFilters(['message' => 'cart']));

        $this->assertSame('SELECT COUNT(lg.id_log) FROM ps_log lg WHERE `message` LIKE :message', $qb->getSQL());
        $this->assertSame(['message' => '%cart%'], $qb->getParameters());
    }

    public function testCountQueryJoinsEmployeeWhenFilteredOnIt(): void
    {
        $qb = $this->buildQueryBuilder()->getCountQueryBuilder($this->getFilters(['employee' => 'doe']));

        $this->assertStringContainsString(
            'LEFT JOIN ps_employee e ON e.id_employee = lg.id_employee',
            $qb->getSQL()
        );
        $this->assertSame(['employee' => '%doe%'], $qb->getParameters());
    }

    public function testSearchQueryKeepsEmployeeAndContextColumns(): void
    {
        $sql = $this->buildQueryBuilder()->getSearchQueryBuilder($this->getFilters([]))->getSQL();

        $this->assertStringContainsString('LEFT JOIN ps_employee e ON e.id_employee = lg.id_employee', $sql);
        $this->assertStringContainsString('as shop_name', $sql);
        $this->assertStringContainsString('as language', $sql);
        $this->assertStringContainsString('as shop_group_name', $sql);
    }

    private function buildQueryBuilder(): LogQueryBuilder
    {
        $connection = $this->createMock(Connection::class);
        $connection->method('getDatabasePlatform')->willReturn(new MySQLPlatform());
        $connection->method('createQueryBuilder')->willReturnCallback(
            fn () => new QueryBuilder($connection)
        );

        return new LogQueryBuilder($connection, self::DB_PREFIX, new DoctrineSearchCriteriaApplicator());
    }

    private function getFilters(array $filters): LogsFilters
    {
        return new LogsFilters(['filters' => $filters] + LogsFilters::getDefaults());
    }
}
