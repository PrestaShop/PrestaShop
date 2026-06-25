<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\BaseRangeCostCalculator;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class BaseRangeCostCalculatorTest extends TestCase
{
    /** @var CarrierDataProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $carrierDataProvider;

    /** @var BaseRangeCostCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->carrierDataProvider = $this->createMock(CarrierDataProviderInterface::class);
        $this->calculator = new BaseRangeCostCalculator($this->carrierDataProvider);
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContextWithData();
        $context->setAvailable(false);

        $this->carrierDataProvider->expects($this->never())->method('getRangeCost');

        $this->calculator->compute($context);
    }

    public function testItDoesNotComputeIfFreeShippingIsAlreadySet(): void
    {
        $context = $this->createMinimalContext();
        $context->setFreeShipping(true);

        $this->carrierDataProvider->expects($this->never())->method('getRangeCost');

        $this->calculator->compute($context);
    }

    public function testItSetsUnavailableIfCarrierDataOrZoneIdAreMissing(): void
    {
        $context = $this->createMinimalContext();

        $this->carrierDataProvider->expects($this->never())->method('getRangeCost');

        $this->calculator->compute($context);

        $this->assertFalse($context->isAvailable());
    }

    public function testItSetsUnavailableIfCostIsNull(): void
    {
        $context = $this->createContextWithData();

        $this->carrierDataProvider->method('getRangeCost')->willReturn(null);

        $this->calculator->compute($context);

        $this->assertFalse($context->isAvailable());
        $this->assertFalse($context->isFreeShipping());
    }

    public function testItSetsFreeShippingIfCostIsZero(): void
    {
        $context = $this->createContextWithData();

        $this->carrierDataProvider->method('getRangeCost')->willReturn(new DecimalNumber('0'));

        $this->calculator->compute($context);

        $this->assertTrue($context->isFreeShipping());
    }

    public function testItSetsCostWhenRangeCostIsPositive(): void
    {
        $context = $this->createContextWithData();
        $cost = new DecimalNumber('15.50');

        $this->carrierDataProvider->method('getRangeCost')->willReturn($cost);

        $this->calculator->compute($context);

        $this->assertFalse($context->isFreeShipping());
        $this->assertTrue($context->getCost()->equals($cost));
    }

    private function createMinimalContext(): ShippingCostPriceInterface
    {
        return ShippingCostPrice::createFromRequest(new ShippingCalculationRequest(
            [], 1, null, null, 1, 1, null, 10.0
        ));
    }

    private function createContextWithData(): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            2, // zoneId
            null, // addressId
            2, // countryZoneId
            1, // currencyId
            null, // customerId
            10.0 // orderTotal
        );

        $context = ShippingCostPrice::createFromRequest($request);
        $context->setCarrierData(new CarrierShippingData(1, 0, 0, false, false));

        return $context;
    }
}
