<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Api;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Api\QueryStockParamsCollection;

/**
 * "Display products below low stock level first" puts product_low_stock_alert at the front of the sort.
 * The stock page sends the flag as a stringified boolean, so a comparison against 1 never matched and
 * ticking the box changed nothing.
 */
class QueryStockParamsCollectionTest extends TestCase
{
    /**
     * @dataProvider getValuesThatMeanTheBoxIsTicked
     */
    public function testTheLowStockSortIsAppliedFirst(string $lowStock): void
    {
        $order = $this->sqlOrderFor(['low_stock' => $lowStock, 'order' => ['product_name asc']]);

        self::assertStringContainsString(
            '{product_low_stock_alert} DESC',
            $order,
            sprintf('low_stock=%s did not put low stock products first', $lowStock)
        );
        self::assertStringStartsWith(
            'ORDER BY {product_low_stock_alert} DESC',
            $order,
            'the low stock sort has to come before the column the merchant chose'
        );
        self::assertStringContainsString('{product_name}', $order, 'the chosen sort must survive');
    }

    /**
     * @dataProvider getValuesThatMeanTheBoxIsNotTicked
     */
    public function testTheLowStockSortIsLeftOutOtherwise(string $lowStock): void
    {
        $order = $this->sqlOrderFor(['low_stock' => $lowStock, 'order' => ['product_name asc']]);

        self::assertStringNotContainsString('product_low_stock_alert', $order);
    }

    public function testTheSortIsLeftOutWhenTheFlagIsAbsent(): void
    {
        $order = $this->sqlOrderFor(['order' => ['product_name asc']]);

        self::assertStringNotContainsString('product_low_stock_alert', $order);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getValuesThatMeanTheBoxIsTicked(): iterable
    {
        // What the stock page actually sends, through String(isChecked).
        yield 'the stringified boolean the page sends' => ['true'];
        yield 'the 1 another client would send' => ['1'];
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function getValuesThatMeanTheBoxIsNotTicked(): iterable
    {
        yield 'cleared, as the page sends it' => ['false'];
        yield 'cleared, as a number' => ['0'];
        yield 'empty' => [''];
    }

    /**
     * @param array<string, mixed> $queryParams
     */
    private function sqlOrderFor(array $queryParams): string
    {
        $collection = new QueryStockParamsCollection();
        $collection->fromArray($queryParams);

        return $collection->getSqlOrder();
    }
}
