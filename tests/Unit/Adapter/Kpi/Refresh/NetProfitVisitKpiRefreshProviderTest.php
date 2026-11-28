<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Kpi\Refresh;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Kpi\Refresh\NetProfitVisitKpiRefreshProvider;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Localization\LocaleInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;

class NetProfitVisitKpiRefreshProviderTest extends TestCase
{
    private const SHOP_CONFIGURATION = [
        'PS_CURRENCY_DEFAULT' => '1',
        'PS_COUNTRY_DEFAULT' => '8',
        'CONF_AVERAGE_PRODUCT_MARGIN' => '40',
        'CONF_ORDER_FIXED' => '0.5',
        'CONF_PS_WIREPAYMENT_FIXED' => '0.2',
        'CONF_PS_WIREPAYMENT_FIXED_FOREIGN' => '0.3',
        'CONF_PS_WIREPAYMENT_VAR' => '2',
        'CONF_PS_WIREPAYMENT_VAR_FOREIGN' => '5',
        'CONF_2_SHIP' => '10',
        'CONF_2_SHIP_OVERSEAS' => '20',
    ];

    /**
     * @var StatsRepository|MockObject
     */
    private $statsRepository;

    /**
     * @var ConfigurationInterface|MockObject
     */
    private $kpiConfiguration;

    /**
     * @var LocaleInterface|MockObject
     */
    private $locale;

    private NetProfitVisitKpiRefreshProvider $provider;

    protected function setUp(): void
    {
        $this->statsRepository = $this->createMock(StatsRepository::class);
        $shopContext = $this->createMock(ShopContextInterface::class);
        $shopContext->method('getContextShopIds')->willReturn([1]);
        $this->kpiConfiguration = $this->createMock(ConfigurationInterface::class);
        $shopConfiguration = $this->createMock(ConfigurationInterface::class);
        $shopConfiguration->method('get')->willReturnCallback(
            static fn (string $key) => self::SHOP_CONFIGURATION[$key] ?? null
        );
        $this->locale = $this->createMock(LocaleInterface::class);
        $currencyDataProvider = $this->createMock(CurrencyDataProviderInterface::class);
        $currencyDataProvider->method('getDefaultCurrencyIsoCode')->willReturn('EUR');

        $this->provider = new NetProfitVisitKpiRefreshProvider(
            $this->statsRepository,
            $shopContext,
            $this->kpiConfiguration,
            $shopConfiguration,
            $this->locale,
            $currencyDataProvider
        );
    }

    /**
     * @dataProvider provideOrders
     */
    public function testGetValueWithOrdersSubtractsFeesReadFromShopConfiguration(array $order, float $expectedNetProfitPerVisit): void
    {
        $this->statsRepository->method('countVisits')->willReturn(100);
        $this->statsRepository->method('getTotalSales')->willReturn(1000.0);
        $this->statsRepository->method('getOrdersForExpensesComputation')->willReturn([$order]);
        $this->statsRepository->expects($this->once())
            ->method('getPurchases')
            ->with($this->anything(), $this->anything(), 40, [1])
            ->willReturn(300.0);
        $this->locale->expects($this->once())
            ->method('formatPrice')
            ->with($this->equalToWithDelta($expectedNetProfitPerVisit, 0.0001), 'EUR')
            ->willReturn('formatted');
        // Shop settings must never be read from the KPI cache table
        $this->kpiConfiguration->expects($this->never())->method('get');

        $this->assertSame('formatted', $this->provider->getValue()->getValue());
    }

    public static function provideOrders(): iterable
    {
        $order = [
            'module' => 'ps_wirepayment',
            'carrier_reference' => '2',
            'total_paid_tax_incl' => 120,
            'total_shipping_tax_excl' => 10,
        ];

        // expenses = 0.5 + 0.2 + 120 * 2% + 10 * 10% = 4.1
        yield 'default currency and country' => [
            $order + ['id_currency' => 1, 'id_country' => 8],
            (1000 - 4.1 - 300) / 100,
        ];
        // expenses = 0.5 + 0.3 + 120 * 5% + 10 * 20% = 8.8
        yield 'foreign currency and country' => [
            $order + ['id_currency' => 2, 'id_country' => 5],
            (1000 - 8.8 - 300) / 100,
        ];
    }

    public function testGetValueWithoutVisitorsButWithProfitReturnsInfinity(): void
    {
        $this->statsRepository->method('countVisits')->willReturn(0);
        $this->statsRepository->method('getTotalSales')->willReturn(50.0);
        $this->statsRepository->method('getOrdersForExpensesComputation')->willReturn([]);
        $this->statsRepository->method('getPurchases')->willReturn(0.0);

        $this->assertSame('&infin;', $this->provider->getValue()->getValue());
    }

    public function testGetValueCachesValueInKpiConfiguration(): void
    {
        $this->statsRepository->method('countVisits')->willReturn(0);
        $this->statsRepository->method('getOrdersForExpensesComputation')->willReturn([]);
        $this->locale->method('formatPrice')->willReturn('€0.00');
        $this->kpiConfiguration->expects($this->exactly(2))
            ->method('set')
            ->with($this->logicalOr('NETPROFIT_VISIT', 'NETPROFIT_VISIT_EXPIRE'));

        $this->assertSame('€0.00', $this->provider->getValue()->getValue());
    }
}
