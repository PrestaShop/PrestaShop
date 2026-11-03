<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\AvgCustomerAgeKpiRefreshProvider;

class AvgCustomerAgeKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideAverageAges
     */
    public function testGetValueRoundsTheAgeInYearsAndCachesItPerLanguage(float $averageAgeInDays, string $expectedValue): void
    {
        $this->statsRepository->method('getAverageCustomerAgeInDays')->willReturn($averageAgeInDays);

        $provider = new AvgCustomerAgeKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext()
        );

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValuePerLanguage('AVG_CUSTOMER_AGE', $expectedValue, '+1 day');
    }

    public static function provideAverageAges(): iterable
    {
        yield '34 years' => [12410.0, '34 years'];
        yield 'rounded up' => [12600.0, '35 years'];
        yield 'no customer' => [0.0, '0 years'];
    }
}
