<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\DisabledProductsKpiRefreshProvider;

class DisabledProductsKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueReturnsThePercentageOfDisabledProducts(array $counters, string $expectedValue): void
    {
        $this->statsRepository->method('getProductActivationCounters')->willReturn($counters);

        $provider = new DisabledProductsKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator()
        );
        $value = $provider->getValue();

        $this->assertSame($expectedValue, $value->getValue());
        $this->assertSame($expectedValue . ' of your products are disabled and not visible to your customers', $value->getTooltip());
        $this->assertCachedValue('DISABLED_PRODUCTS', $expectedValue, '+2 hour');
    }

    public static function provideCounters(): iterable
    {
        yield 'one quarter' => [['disabled' => 3, 'total' => 12], '25%'];
        yield 'rounded to two decimals' => [['disabled' => 1, 'total' => 3], '33.33%'];
        // Legacy divided by zero here
        yield 'no product' => [['disabled' => 0, 'total' => 0], '0%'];
    }
}
