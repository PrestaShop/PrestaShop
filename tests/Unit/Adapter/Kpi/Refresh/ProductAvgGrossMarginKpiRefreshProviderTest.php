<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\ProductAvgGrossMarginKpiRefreshProvider;

class ProductAvgGrossMarginKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideMargins
     */
    public function testGetValueReturnsTheMarginAsPercentage(float $margin, string $expectedValue): void
    {
        $this->statsRepository->method('getProductAverageGrossMargin')->willReturn($margin);

        $provider = new ProductAvgGrossMarginKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator()
        );
        $value = $provider->getValue();

        $this->assertSame($expectedValue, $value->getValue());
        $this->assertStringContainsString('you will retain $' . $expectedValue . ' to cover', $value->getTooltip());
        $this->assertCachedValue('PRODUCT_AVG_GROSS_MARGIN', $expectedValue, '+6 hour');
    }

    public static function provideMargins(): iterable
    {
        yield 'margin' => [0.255, '25.5%'];
        yield 'rounded to two decimals' => [0.12345, '12.35%'];
        yield 'no product' => [0.0, '0%'];
    }
}
