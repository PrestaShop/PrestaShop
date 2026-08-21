<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Provider;

use Currency;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Provider\ConfigFreeShippingCriteriaProvider;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\HookManager;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class ConfigFreeShippingCriteriaProviderTest extends TestCase
{
    /** @var HookManager|\PHPUnit\Framework\MockObject\MockObject */
    private $hookManager;

    /** @var AdapterConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private $configuration;

    /** @var Tools|\PHPUnit\Framework\MockObject\MockObject */
    private $tools;

    /** @var CurrencyRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $currencyRepository;

    /** @var ConfigFreeShippingCriteriaProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->hookManager = $this->createMock(HookManager::class);
        $this->configuration = $this->createMock(AdapterConfiguration::class);
        $this->tools = $this->createMock(Tools::class);
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);

        $this->provider = new ConfigFreeShippingCriteriaProvider(
            $this->hookManager,
            $this->configuration,
            $this->tools,
            $this->currencyRepository
        );
    }

    public function testGetCriteriaConvertsPriceBeforeHookAndUsesResolvedZoneId(): void
    {
        $context = $this->createContext(2, 5); // currencyId = 2, zoneId = 5

        $this->configuration->method('get')->willReturnCallback(function ($key) {
            if ($key === 'PS_SHIPPING_FREE_PRICE') {
                return '100.00';
            }
            if ($key === 'PS_SHIPPING_FREE_WEIGHT') {
                return '10.5';
            }

            return false;
        });

        $currency = $this->createMock(Currency::class);
        $this->currencyRepository->expects($this->once())
            ->method('get')
            ->with(new CurrencyId(2))
            ->willReturn($currency);

        $this->tools->expects($this->once())
            ->method('convertPrice')
            ->with(100.0, $currency)
            ->willReturn(120.0);

        $this->hookManager->expects($this->exactly(2))
            ->method('exec')
            ->willReturnCallback(function (string $hookName, array $params) {
                if ($hookName === 'actionOverrideShippingFreePrice') {
                    $this->assertEquals(120.0, $params['shippingFreePrice']);
                    $this->assertEquals(5, $params['id_zone']);
                    $this->assertEquals(2, $params['id_currency']);
                } elseif ($hookName === 'actionOverrideShippingFreeWeight') {
                    $this->assertEquals('10.5', $params['shippingFreeWeight']);
                    $this->assertEquals(5, $params['id_zone']);
                    $this->assertEquals(2, $params['id_currency']);
                }
            });

        $criteria = $this->provider->getCriteria($context);

        $this->assertTrue($criteria->hasFreePrice());
        $this->assertEquals(new DecimalNumber('120'), $criteria->getFreePrice());
        $this->assertTrue($criteria->hasFreeWeight());
        $this->assertEquals(new DecimalNumber('10.5'), $criteria->getFreeWeight());
    }

    public function testGetCriteriaFallsBackToCountryZoneIdWhenResolvedZoneIdIsNull(): void
    {
        $context = $this->createContext(2, null, 7); // zoneId = null (unresolved), countryZoneId = 7

        $this->configuration->method('get')->willReturn(false);

        $zoneIdsPassedToHooks = [];
        $this->hookManager->expects($this->exactly(2))
            ->method('exec')
            ->willReturnCallback(function (string $hookName, array $params) use (&$zoneIdsPassedToHooks) {
                $zoneIdsPassedToHooks[$hookName] = $params['id_zone'];
            });

        $this->provider->getCriteria($context);

        $this->assertSame(7, $zoneIdsPassedToHooks['actionOverrideShippingFreePrice']);
        $this->assertSame(7, $zoneIdsPassedToHooks['actionOverrideShippingFreeWeight']);
    }

    public function testGetCriteriaDoesNotConvertPriceWhenFreePriceIsDisabled(): void
    {
        $context = $this->createContext(2, 5);

        $this->configuration->method('get')->willReturn(false);

        $this->currencyRepository->expects($this->never())->method('get');
        $this->tools->expects($this->never())->method('convertPrice');

        $criteria = $this->provider->getCriteria($context);

        $this->assertFalse($criteria->hasFreePrice());
        $this->assertFalse($criteria->hasFreeWeight());
    }

    public function testGetCriteriaAppliesModuleOverrideThroughHookReferenceParameters(): void
    {
        $context = $this->createContext(2, 5);

        $this->configuration->method('get')->willReturnCallback(function ($key) {
            if ($key === 'PS_SHIPPING_FREE_PRICE') {
                return '100.00';
            }
            if ($key === 'PS_SHIPPING_FREE_WEIGHT') {
                return '10.5';
            }

            return false;
        });

        $currency = $this->createMock(Currency::class);
        $this->currencyRepository->method('get')->willReturn($currency);
        $this->tools->method('convertPrice')->willReturn(120.0);

        $this->hookManager->method('exec')->willReturnCallback(function (string $hookName, array $params) {
            if ($hookName === 'actionOverrideShippingFreePrice') {
                $params['shippingFreePrice'] = 42.0;
            } elseif ($hookName === 'actionOverrideShippingFreeWeight') {
                $params['shippingFreeWeight'] = 7.0;
            }
        });

        $criteria = $this->provider->getCriteria($context);

        $this->assertEquals(new DecimalNumber('42'), $criteria->getFreePrice());
        $this->assertEquals(new DecimalNumber('7'), $criteria->getFreeWeight());
    }

    private function createContext(int $currencyId, ?int $zoneId = null, int $countryZoneId = 0): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            $zoneId,
            null, // addressId
            $countryZoneId,
            $currencyId,
            null, // customerId
            100.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
