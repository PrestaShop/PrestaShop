<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\HandlingCostCalculator;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class HandlingCostCalculatorTest extends TestCase
{
    /** @var AdapterConfiguration|\PHPUnit\Framework\MockObject\MockObject */
    private $configuration;

    /** @var HandlingCostCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->configuration = $this->createMock(AdapterConfiguration::class);
        $this->calculator = new HandlingCostCalculator($this->configuration);
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext();
        $context->setAvailable(false);

        $this->configuration->expects($this->never())->method('get');

        $this->calculator->compute($context);
    }

    public function testItDoesNotComputeIfFreeShippingIsAlreadySet(): void
    {
        $context = $this->createContext();
        $context->setFreeShipping(true);

        $this->configuration->expects($this->never())->method('get');

        $this->calculator->compute($context);
    }

    public function testItDoesNotComputeIfCarrierHandlingIsNotEnabled(): void
    {
        $context = $this->createContext();
        $context->setCarrierData(new CarrierShippingData(1, 0, 0, false, false));

        $this->configuration->expects($this->never())->method('get');

        $this->calculator->compute($context);
    }

    public function testItAddsHandlingCostToTotal(): void
    {
        $context = $this->createContext();
        $context->setCarrierData(new CarrierShippingData(1, 0, 0, true, false));
        $context->setCost(new DecimalNumber('10.00'));

        $this->configuration->method('get')->with('PS_SHIPPING_HANDLING')->willReturn('5.00');

        $this->calculator->compute($context);

        $this->assertTrue($context->getCost()->equals(new DecimalNumber('15.00')));
    }

    private function createContext(): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            1, // zoneId
            null, // addressId
            1, // countryZoneId
            1, // currencyId
            null, // customerId
            10.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
