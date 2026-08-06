<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\CarrierDataCalculator;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Provider\CarrierShippingData;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class CarrierDataCalculatorTest extends TestCase
{
    /** @var CarrierDataProviderInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $carrierDataProvider;

    /** @var CarrierDataCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->carrierDataProvider = $this->createMock(CarrierDataProviderInterface::class);
        $this->calculator = new CarrierDataCalculator($this->carrierDataProvider);
    }

    public function testItSetsUnavailableIfCarrierNotFound(): void
    {
        $context = $this->createContext(1);
        $this->carrierDataProvider->method('getCarrierShippingData')->willReturn(null);

        $this->calculator->compute($context);

        $this->assertFalse($context->isAvailable());
    }

    public function testItSetsCarrierDataInContext(): void
    {
        $context = $this->createContext(1);
        $carrierData = new CarrierShippingData(1, 0, 0, false, false);

        $this->carrierDataProvider->method('getCarrierShippingData')->willReturn($carrierData);

        $this->calculator->compute($context);

        $this->assertSame($carrierData, $context->getCarrierData());
    }

    public function testItSetsFreeShippingIfCarrierIsFree(): void
    {
        $context = $this->createContext(1);
        $carrierData = new CarrierShippingData(1, 0, 0, false, true);

        $this->carrierDataProvider->method('getCarrierShippingData')->willReturn($carrierData);

        $this->calculator->compute($context);

        $this->assertTrue($context->isFreeShipping());
    }

    private function createContext(int $carrierId): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            $carrierId,
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
