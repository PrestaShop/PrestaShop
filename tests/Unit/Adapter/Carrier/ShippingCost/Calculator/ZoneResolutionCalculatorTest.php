<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Address\Repository\AddressRepository;
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\ZoneResolutionCalculator;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class ZoneResolutionCalculatorTest extends TestCase
{
    /** @var AddressRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $addressRepository;

    /** @var ZoneResolutionCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->addressRepository = $this->createMock(AddressRepository::class);
        $this->calculator = new ZoneResolutionCalculator($this->addressRepository);
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext(10);
        $context->setAvailable(false);

        $this->addressRepository->expects($this->never())->method('getZoneId');

        $this->calculator->compute($context);
    }

    public function testItDoesNotResolveIfAlreadySet(): void
    {
        $context = $this->createContext(null);
        $context->setResolvedZoneId(5);

        $this->addressRepository->expects($this->never())->method('getZoneId');

        $this->calculator->compute($context);
        $this->assertSame(5, $context->getResolvedZoneId());
    }

    public function testItResolvesFromAddressRepository(): void
    {
        $context = $this->createContext(10); // Address ID 10

        $this->addressRepository->method('getZoneId')
            ->with(new AddressId(10))
            ->willReturn(3);

        $this->calculator->compute($context);
        $this->assertSame(3, $context->getResolvedZoneId());
        $this->assertTrue($context->isAvailable());
    }

    public function testItResolvesFromCountryZoneFallback(): void
    {
        $context = $this->createContext(null); // No Address ID
        // Assuming default country zone ID is 2

        $this->addressRepository->expects($this->never())->method('getZoneId');

        $this->calculator->compute($context);
        $this->assertSame(2, $context->getResolvedZoneId());
        $this->assertTrue($context->isAvailable());
    }

    public function testItSetsUnavailableIfZoneCannotBeResolved(): void
    {
        $context = $this->createContext(10);

        $this->addressRepository->method('getZoneId')
            ->willThrowException(new \PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException());

        $this->calculator->compute($context);

        $this->assertFalse($context->isAvailable());
        $this->assertNull($context->getResolvedZoneId());
    }

    private function createContext(?int $addressId): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            null, // zoneId
            $addressId,
            2, // countryZoneId (default)
            1, // currencyId
            null, // customerId
            10.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
