<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Api;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Api\QueryStockMovementParamsCollection;
use PrestaShopBundle\Api\QueryStockParamsCollection;

/**
 * The movements list offers the same status filter as the stock list, and the shared andWhere() already
 * maps {active} onto p.active. Leaving 'active' out of the movements collection made
 * excludeUnknownParams() drop it before it could be used, so choosing a status changed nothing.
 */
class QueryStockMovementParamsCollectionTest extends TestCase
{
    /**
     * @dataProvider getStatuses
     */
    public function testTheStatusFilterReachesTheQuery(string $active): void
    {
        self::assertStringContainsString(
            '{active}',
            $this->whereClauseFor(new QueryStockMovementParamsCollection(), $active),
            sprintf('a status filter of %s was dropped before it could be applied', $active)
        );
    }

    /**
     * The two tabs share the filter, so they should build the same clause for it.
     */
    public function testTheMovementsTabFiltersTheSameWayAsTheStockTab(): void
    {
        self::assertSame(
            $this->whereClauseFor(new QueryStockParamsCollection(), '0'),
            $this->whereClauseFor(new QueryStockMovementParamsCollection(), '0')
        );
    }

    /**
     * Filters the movements list does not offer must still be discarded, so the allow list keeps working.
     */
    public function testAnUnknownFilterIsStillDropped(): void
    {
        $collection = new QueryStockMovementParamsCollection();
        $collection->fromArray(['not_a_filter' => '1', 'page_index' => 1, 'page_size' => 10]);

        $filters = $collection->getSqlFilters();

        self::assertStringNotContainsString('not_a_filter', $filters[$collection::SQL_CLAUSE_WHERE]);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getStatuses(): iterable
    {
        yield 'disabled' => ['0'];
        yield 'enabled' => ['1'];
    }

    private function whereClauseFor(QueryStockParamsCollection $collection, string $active): string
    {
        $collection->fromArray(['active' => $active, 'page_index' => 1, 'page_size' => 10]);

        $filters = $collection->getSqlFilters();

        return $filters[$collection::SQL_CLAUSE_WHERE];
    }
}
