<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\Sales8020KpiRefreshProvider;

class Sales8020KpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueReturnsTheShareOfTheCatalogSold(int $distinctProducts, int $totalProducts, string $expectedPercent): void
    {
        $this->statsRepository->method('countDistinctProductsSold')->willReturn($distinctProducts);
        $this->statsRepository->method('getProductActivationCounters')->willReturn(['disabled' => 0, 'total' => $totalProducts]);

        $provider = new Sales8020KpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator()
        );
        $value = $provider->getValue();

        // The translator keeps the doubled percent sign of the message, as in legacy
        $expectedValue = $expectedPercent . '% of your Catalog';
        $this->assertSame($expectedValue, $value->getValue());
        $this->assertSame(
            'Within your catalog, ' . $expectedPercent . ' of your products have had sales in the last 30 days',
            $value->getTooltip()
        );
        $this->assertCachedValue('8020_SALES_CATALOG', $expectedValue, '+12 hour');
    }

    public static function provideCounters(): iterable
    {
        yield 'one quarter' => [5, 20, '25%'];
        yield 'rounded without decimals' => [1, 3, '33%'];
        yield 'no sale' => [0, 20, '0%'];
        // Legacy divided by zero here
        yield 'no product' => [2, 0, '0%'];
    }
}
