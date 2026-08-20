<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use PrestaShop\PrestaShop\Core\Grid\Query\OrderQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The count query only needs the joins that can exclude an order. The rest are LEFT joins matching at
 * most one row each, so they cannot change the count, and on a large orders table they cost real time.
 */
class OrderQueryBuilderTest extends KernelTestCase
{
    private function buildQueryBuilder(): OrderQueryBuilder
    {
        self::bootKernel();
        $container = self::getContainer();

        return new OrderQueryBuilder(
            $container->get('doctrine.dbal.default_connection'),
            _DB_PREFIX_,
            $container->get('prestashop.core.query.doctrine_search_criteria_applicator'),
            1,
            [1]
        );
    }

    private function countSql(array $filters): string
    {
        return $this->buildQueryBuilder()->getCountQueryBuilder(new OrderFilters([
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_order',
            'sortOrder' => 'asc',
            'filters' => $filters,
        ]))->getSQL();
    }

    public function testItCountsWithoutTheJoinsThatOnlySupplyColumns(): void
    {
        $sql = $this->countSql([]);

        // These can exclude an order, so they decide the count.
        $this->assertStringContainsString(_DB_PREFIX_ . 'address', $sql);
        $this->assertStringContainsString(_DB_PREFIX_ . 'country', $sql);

        $this->assertStringNotContainsString(_DB_PREFIX_ . 'currency', $sql);
        $this->assertStringNotContainsString(_DB_PREFIX_ . 'shop s', $sql);
        $this->assertStringNotContainsString(_DB_PREFIX_ . 'order_state_lang', $sql);
        $this->assertStringNotContainsString(_DB_PREFIX_ . 'customer', $sql);
    }

    /**
     * @dataProvider provideFiltersNeedingAJoin
     */
    public function testItKeepsTheJoinAFilterReadsFrom(array $filters, string $expectedTable): void
    {
        $this->assertStringContainsString(_DB_PREFIX_ . $expectedTable, $this->countSql($filters));
    }

    public function provideFiltersNeedingAJoin(): iterable
    {
        yield 'order state' => [['osname' => 2], 'order_state'];
        yield 'customer name' => [['customer' => 'doe'], 'customer'];
        yield 'company' => [['company' => 'acme'], 'customer'];
    }

    /**
     * @dataProvider provideFilters
     */
    public function testItRunsForEveryFilter(array $filters): void
    {
        $count = $this->buildQueryBuilder()->getCountQueryBuilder(new OrderFilters([
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_order',
            'sortOrder' => 'asc',
            'filters' => $filters,
        ]))->executeQuery()->fetchOne();

        $this->assertIsNumeric($count);
    }

    public function provideFilters(): iterable
    {
        yield 'none' => [[]];
        yield 'order state' => [['osname' => 2]];
        yield 'customer' => [['customer' => 'doe']];
        yield 'company' => [['company' => 'acme']];
        yield 'reference' => [['reference' => 'ABC']];
        yield 'new customer' => [['new' => 1]];
        yield 'country' => [['country_name' => 1]];
        yield 'date' => [['date_add' => ['from' => '2000-01-01']]];
    }
}
