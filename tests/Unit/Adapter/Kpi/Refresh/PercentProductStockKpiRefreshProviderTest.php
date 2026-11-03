<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\PercentProductStockKpiRefreshProvider;

class PercentProductStockKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueReturnsThePercentageOfProductsInStock(array $counters, string $expectedValue): void
    {
        $this->statsRepository->method('getProductStockCounters')->with(self::SHOP_IDS)->willReturn($counters);

        $provider = new PercentProductStockKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValue('PERCENT_PRODUCT_STOCK', $expectedValue, '+4 hour');
    }

    public static function provideCounters(): iterable
    {
        yield 'three quarters' => [['with_stock' => 3, 'total' => 4], '75%'];
        yield 'rounded to two decimals' => [['with_stock' => 1, 'total' => 3], '33.33%'];
        yield 'no product' => [['with_stock' => 0, 'total' => 0], '0%'];
    }
}
