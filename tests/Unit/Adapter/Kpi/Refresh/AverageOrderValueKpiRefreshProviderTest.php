<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\AverageOrderValueKpiRefreshProvider;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;

class AverageOrderValueKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueFormatsTheAverageAmount(array $counters, float $expectedAmount): void
    {
        $this->statsRepository->method('getAverageOrderValueCounters')->willReturn($counters);
        $locale = $this->createMock(LocaleInterface::class);
        $locale->expects($this->once())
            ->method('formatPrice')
            ->with($expectedAmount, 'EUR')
            ->willReturn('€25.00');
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getDefaultCurrencyIsoCode')->willReturn('EUR');

        $provider = new AverageOrderValueKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $locale,
            $currencyDataProvider
        );

        $this->assertSame('€25.00', $provider->getValue()->getValue());
        $this->assertCachedValue('AVG_ORDER_VALUE', '€25.00', date('Y-m-d 00:00:00', strtotime('+1 day')));
    }

    public static function provideCounters(): iterable
    {
        yield 'orders' => [['orders' => 4, 'total_paid_tax_excl' => 100.0], 25.0];
        yield 'no orders' => [['orders' => 0, 'total_paid_tax_excl' => 0.0], 0.0];
    }
}
