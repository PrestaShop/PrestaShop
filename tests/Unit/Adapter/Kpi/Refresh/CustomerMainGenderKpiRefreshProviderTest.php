<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\CustomerMainGenderKpiRefreshProvider;

class CustomerMainGenderKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounters
     */
    public function testGetValueNamesTheMainGenderAndCachesItPerLanguage(?array $counters, string $expectedValue): void
    {
        $this->statsRepository->method('getCustomerGenderCounters')->willReturn($counters);

        $provider = new CustomerMainGenderKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator(),
            $this->createLanguageContext()
        );

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValuePerLanguage('CUSTOMER_MAIN_GENDER', $expectedValue, '+1 day');
    }

    public static function provideCounters(): iterable
    {
        yield 'male' => [['total' => 10, 'male' => 6, 'female' => 3, 'neutral' => 1], '60% Male Customers'];
        yield 'female' => [['total' => 3, 'male' => 1, 'female' => 2, 'neutral' => 0], '67% Female Customers'];
        yield 'tie goes to female' => [['total' => 10, 'male' => 5, 'female' => 5, 'neutral' => 0], '50% Female Customers'];
        yield 'neutral' => [['total' => 4, 'male' => 1, 'female' => 1, 'neutral' => 2], '50% Neutral Customers'];
        yield 'no customer' => [null, 'No customers'];
    }
}
