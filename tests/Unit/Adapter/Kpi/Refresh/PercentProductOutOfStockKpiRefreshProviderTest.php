<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\PercentProductOutOfStockKpiRefreshProvider;

class PercentProductOutOfStockKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueReturnsThePercentageOfProductsOutOfStock(array $counters, string $expectedValue): void
    {
        $this->statsRepository->method('getProductStockCounters')->willReturn($counters);

        $provider = new PercentProductOutOfStockKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator()
        );
        $value = $provider->getValue();

        $this->assertSame($expectedValue, $value->getValue());
        $this->assertSame($expectedValue . ' of your products for sale are out of stock.', $value->getTooltip());
        $this->assertCachedValue('PERCENT_PRODUCT_OUT_OF_STOCK', $expectedValue, '+4 hour');
    }

    public static function provideCounters(): iterable
    {
        yield 'one quarter' => [['with_stock' => 3, 'total' => 4], '25%'];
        yield 'all' => [['with_stock' => 0, 'total' => 3], '100%'];
        yield 'no product' => [['with_stock' => 0, 'total' => 0], '0%'];
    }
}
