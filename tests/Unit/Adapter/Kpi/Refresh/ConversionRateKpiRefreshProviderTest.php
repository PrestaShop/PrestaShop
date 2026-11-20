<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\ConversionRateKpiRefreshProvider;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;

class ConversionRateKpiRefreshProviderTest extends TestCase
{
    /**
     * @var StatsRepository|MockObject
     */
    private $statsRepository;

    /**
     * @var ShopContextInterface|MockObject
     */
    private $shopContext;

    /**
     * @var ConfigurationInterface|MockObject
     */
    private $configuration;

    private ConversionRateKpiRefreshProvider $provider;

    protected function setUp(): void
    {
        $this->statsRepository = $this->createMock(StatsRepository::class);
        $this->shopContext = $this->createMock(ShopContextInterface::class);
        $this->shopContext->method('getContextShopIds')->willReturn([1]);
        $this->configuration = $this->createMock(ConfigurationInterface::class);

        $this->provider = new ConversionRateKpiRefreshProvider(
            $this->statsRepository,
            $this->shopContext,
            $this->configuration
        );
    }

    public function testItComputesThePercentageWhenThereAreVisitors(): void
    {
        $this->statsRepository->method('countVisits')->willReturn(200);
        $this->statsRepository->method('countOrders')->willReturn(10);

        $value = $this->provider->getValue();

        $this->assertSame('5%', $value->getValue());
        $this->assertNull($value->getTooltip());
    }

    public function testItReturnsInfinityWhenThereAreOrdersButNoVisitors(): void
    {
        $this->statsRepository->method('countVisits')->willReturn(0);
        $this->statsRepository->method('countOrders')->willReturn(3);

        $value = $this->provider->getValue();

        $this->assertSame('&infin;%', $value->getValue());
    }

    public function testItReturnsZeroWhenThereIsNoActivity(): void
    {
        $this->statsRepository->method('countVisits')->willReturn(0);
        $this->statsRepository->method('countOrders')->willReturn(0);

        $value = $this->provider->getValue();

        $this->assertSame('0%', $value->getValue());
    }
}
