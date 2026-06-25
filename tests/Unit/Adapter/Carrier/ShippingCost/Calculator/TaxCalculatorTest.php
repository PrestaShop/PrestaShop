<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use Currency;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\TaxCalculator;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\ShippingTaxRateProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Pricing\Rounding\RoundingServiceInterface;
use PrestaShop\PrestaShop\Core\Pricing\ValueObject\TaxRate;

class TaxCalculatorTest extends TestCase
{
    /** @var AdapterConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private $configuration;

    /** @var ShippingTaxRateProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $taxRateProvider;

    /** @var CurrencyRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $currencyRepository;

    /** @var RoundingServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $roundingService;

    /** @var TaxCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(AdapterConfiguration::class);
        $this->taxRateProvider = $this->createMock(ShippingTaxRateProviderInterface::class);
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->roundingService = $this->createMock(RoundingServiceInterface::class);
        $this->calculator = new TaxCalculator(
            $this->configuration,
            $this->taxRateProvider,
            $this->currencyRepository,
            $this->roundingService
        );
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext(1);
        $context->setAvailable(false);

        $this->currencyRepository->expects($this->never())->method('get');

        $this->calculator->compute($context);
    }

    public function testItSetsZeroTaxesIfFreeShipping(): void
    {
        $context = $this->createContext(1);
        $context->setFreeShipping(true);

        $currency = $this->createMock(Currency::class);
        $currency->precision = 2;
        $this->currencyRepository->method('get')->willReturn($currency);

        $this->calculator->compute($context);

        $this->assertTrue($context->getTaxExcluded()->equalsZero());
        $this->assertTrue($context->getTaxIncluded()->equalsZero());
    }

    public function testItCalculatesTaxesCorrectly(): void
    {
        $context = $this->createContext(1);
        $context->setCost(new DecimalNumber('10.00'));

        $currency = $this->createMock(Currency::class);
        $currency->precision = 2;
        $this->currencyRepository->method('get')->willReturn($currency);

        $this->configuration->method('get')->willReturnCallback(function ($key) {
            $map = [
                'PS_TAX' => true,
                'PS_ATCP_SHIPWRAP' => false,
            ];

            return $map[$key] ?? null;
        });
        $this->taxRateProvider->method('getTaxRate')->willReturn(new TaxRate(new DecimalNumber('20'))); // 20% tax

        $this->roundingService->method('round')->willReturnCallback(fn (DecimalNumber $number) => $number);

        $this->calculator->compute($context);

        $this->assertTrue($context->getTaxExcluded()->equals(new DecimalNumber('10.00')));
        $this->assertTrue($context->getTaxIncluded()->equals(new DecimalNumber('12.00')));
    }

    private function createContext(int $currencyId): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            1, // zoneId
            1, // addressId
            1, // countryZoneId
            $currencyId,
            null, // customerId
            10.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
