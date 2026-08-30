<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\TotalShipmentPriceCalculator;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class TotalShipmentPriceCalculatorTest extends TestCase
{
    private TotalShipmentPriceCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new TotalShipmentPriceCalculator();
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext([]);
        $context->setAvailable(false);

        $this->calculator->compute($context);

        $this->assertTrue($context->getShipmentTotal()->equals(new DecimalNumber('10.0')));
    }

    public function testItReturnsEarlyIfFreeShipping(): void
    {
        $context = $this->createContext([
            ['id_product' => 1, 'price_wt' => 10.0, 'quantity' => 2, 'is_virtual' => false],
        ]);
        $context->setFreeShipping(true);

        $this->calculator->compute($context);

        $this->assertTrue($context->getShipmentTotal()->equals(new DecimalNumber('10.0')));
    }

    public function testItComputesTotalPriceCorrectly(): void
    {
        $products = [
            ['id_product' => 1, 'price_wt' => 1.5, 'quantity' => 2, 'is_virtual' => false],
            ['id_product' => 2, 'price_wt' => 0.5, 'quantity' => 3, 'is_virtual' => false],
            ['id_product' => 3, 'price_wt' => 10.0, 'quantity' => 1, 'is_virtual' => true], // Should be filtered out by ShippingCostPrice
        ];

        $context = $this->createContext($products);
        $this->calculator->compute($context);

        // (1.5 * 2) + (0.5 * 3) = 3.0 + 1.5 = 4.5
        $this->assertTrue($context->getShipmentTotal()->equals(new DecimalNumber('4.5')));
    }

    private function createContext(array $products): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            $products,
            1, // carrierId
            1, // zoneId
            null, // addressId
            1, // countryZoneId
            1, // currencyId
            null, // customerId
            10.0 // shipmentTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
