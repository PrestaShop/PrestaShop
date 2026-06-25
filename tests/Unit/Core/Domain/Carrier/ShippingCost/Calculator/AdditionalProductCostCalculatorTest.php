<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Carrier\ShippingCost\Calculator;

use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\AdditionalProductCostCalculator;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;

class AdditionalProductCostCalculatorTest extends TestCase
{
    private AdditionalProductCostCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new AdditionalProductCostCalculator();
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext([]);
        $context->setAvailable(false);

        $this->calculator->compute($context);

        $this->assertTrue($context->getCost()->equals(new DecimalNumber('0')));
    }

    public function testItReturnsEarlyIfFreeShipping(): void
    {
        $context = $this->createContext([
            ['id_product' => 1, 'additional_shipping_cost' => 5.0, 'quantity' => 1, 'is_virtual' => false],
        ]);
        $context->setFreeShipping(true);

        $this->calculator->compute($context);

        $this->assertTrue($context->getCost()->equals(new DecimalNumber('0')));
    }

    public function testItAddsAdditionalCostCorrectly(): void
    {
        $products = [
            ['id_product' => 1, 'additional_shipping_cost' => 5.0, 'quantity' => 2, 'is_virtual' => false], // 10.0
            ['id_product' => 2, 'additional_shipping_cost' => 2.5, 'quantity' => 1, 'is_virtual' => false], // 2.5
            ['id_product' => 3, 'additional_shipping_cost' => 10.0, 'quantity' => 1, 'is_virtual' => true], // Ignored
        ];

        $context = $this->createContext($products);
        $context->setCost(new DecimalNumber('10.00')); // Base cost

        $this->calculator->compute($context);

        // 10.00 + 10.0 + 2.5 = 22.5
        $this->assertTrue($context->getCost()->equals(new DecimalNumber('22.5')));
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
            10.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
