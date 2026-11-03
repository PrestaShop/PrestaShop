<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\AvgMsgResponseTimeKpiRefreshProvider;

class AvgMsgResponseTimeKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideDelays
     */
    public function testGetValueAveragesTheResponseDelayInHours(array $rows, string $expectedValue): void
    {
        $this->statsRepository->method('getMessageResponseDelays')->willReturn($rows);

        $provider = new AvgMsgResponseTimeKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->kpiConfiguration,
            $this->createTranslator()
        );

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValue('AVG_MSG_RESPONSE_TIME', $expectedValue, '+4 hour');
    }

    public static function provideDelays(): iterable
    {
        yield 'one and two hours' => [
            [
                ['question' => '2026-01-01 10:00:00', 'reply' => '2026-01-01 11:00:00'],
                ['question' => '2026-01-01 10:00:00', 'reply' => '2026-01-01 12:00:00'],
            ],
            '1.5 hours',
        ];
        yield 'rounded to one decimal' => [
            [['question' => '2026-01-01 10:00:00', 'reply' => '2026-01-01 10:20:00']],
            '0.3 hours',
        ];
        yield 'no message' => [[], '0 hours'];
    }
}
