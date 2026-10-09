<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineSearchCriteriaApplicatorInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\OrderQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;

class OrderQueryBuilderTest extends TestCase
{
    private const DB_PREFIX = 'ps_';

    /**
     * The "new customer" column must be computed with a short-circuiting NOT EXISTS
     * subquery rather than a full COUNT over every previous order of the customer.
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/40672
     */
    public function testNewCustomerSubSelectUsesExists(): void
    {
        $queryBuilder = new OrderQueryBuilder(
            $this->getMockConnection(),
            self::DB_PREFIX,
            $this->getMockCriteriaApplicator(),
            1,
            [1]
        );

        $sql = $queryBuilder
            ->getSearchQueryBuilder(new OrderFilters(OrderFilters::getDefaults()))
            ->getSQL();

        $this->assertStringContainsStringIgnoringCase('NOT EXISTS', $sql);
        // The old implementation counted every previous order, which is what caused the slow query.
        $this->assertStringNotContainsStringIgnoringCase('count(so.id_order)', $sql);
    }

    private function getMockConnection(): Connection
    {
        $mock = $this->createMock(Connection::class);
        $mock->method('getDatabasePlatform')->willReturn(new MySQL80Platform());

        // A fresh QueryBuilder per call so the inner subquery never shares state with the outer query.
        $mock->method('createQueryBuilder')->willReturnCallback(
            fn (): QueryBuilder => new QueryBuilder($mock)
        );

        return $mock;
    }

    private function getMockCriteriaApplicator(): DoctrineSearchCriteriaApplicatorInterface
    {
        $mock = $this->createMock(DoctrineSearchCriteriaApplicatorInterface::class);
        $mock->method('applyPagination')->willReturnSelf();
        $mock->method('applySorting')->willReturnSelf();
        $mock->method('applyDeterministicSorting')->willReturnSelf();

        return $mock;
    }
}
