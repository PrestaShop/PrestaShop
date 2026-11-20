<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\MessagesPerThreadKpiRefreshProvider;

class MessagesPerThreadKpiRefreshProviderTest extends AbstractKpiRefreshProviderTestCase
{
    /**
     * @dataProvider provideCounts
     */
    public function testGetValueAveragesTheMessagesOfClosedThreads(array $counts, string $expectedValue): void
    {
        $this->statsRepository->method('getMessageCountsPerClosedThread')->willReturn($counts);

        $provider = new MessagesPerThreadKpiRefreshProvider($this->statsRepository, $this->shopContext, $this->kpiConfiguration);

        $this->assertSame($expectedValue, $provider->getValue()->getValue());
        $this->assertCachedValue('MESSAGES_PER_THREAD', $expectedValue, '+12 hour');
    }

    public static function provideCounts(): iterable
    {
        yield 'half' => [[3, 4], '3.5'];
        yield 'rounded to one decimal' => [[1, 1, 2], '1.3'];
        yield 'no thread' => [[], '0'];
    }
}
