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
use PrestaShop\PrestaShop\Adapter\Carrier\ShippingCost\Calculator\CurrencyConversionCalculator;
use PrestaShop\PrestaShop\Adapter\Currency\Repository\CurrencyRepository;
use PrestaShop\PrestaShop\Adapter\Tools;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;

class CurrencyConversionCalculatorTest extends TestCase
{
    /** @var Tools|\PHPUnit\Framework\MockObject\MockObject */
    private $tools;

    /** @var CurrencyRepository|\PHPUnit\Framework\MockObject\MockObject */
    private $currencyRepository;

    /** @var CurrencyConversionCalculator */
    private $calculator;

    protected function setUp(): void
    {
        $this->tools = $this->createMock(Tools::class);
        $this->currencyRepository = $this->createMock(CurrencyRepository::class);
        $this->calculator = new CurrencyConversionCalculator($this->tools, $this->currencyRepository);
    }

    public function testItReturnsEarlyIfAlreadyUnavailable(): void
    {
        $context = $this->createContext(1);
        $context->setAvailable(false);

        $this->tools->expects($this->never())->method('convertPrice');

        $this->calculator->compute($context);
    }

    public function testItDoesNotComputeIfFreeShippingIsAlreadySet(): void
    {
        $context = $this->createContext(1);
        $context->setFreeShipping(true);

        $this->tools->expects($this->never())->method('convertPrice');

        $this->calculator->compute($context);
    }

    public function testItConvertsCostSuccessfully(): void
    {
        $context = $this->createContext(1);
        $initialCost = new DecimalNumber('10.00');
        $context->setCost($initialCost);

        $currency = $this->createMock(Currency::class);
        $this->currencyRepository->method('get')->with(new CurrencyId(1))->willReturn($currency);

        $convertedAmount = 12.50;
        $this->tools->method('convertPrice')->with('10', $currency)->willReturn($convertedAmount);

        $this->calculator->compute($context);

        $this->assertTrue($context->getCost()->equals(new DecimalNumber('12.5')));
    }

    private function createContext(int $currencyId): ShippingCostPriceInterface
    {
        $request = new ShippingCalculationRequest(
            [], // products
            1, // carrierId
            1, // zoneId
            null, // addressId
            1, // countryZoneId
            $currencyId,
            null, // customerId
            10.0 // orderTotal
        );

        return ShippingCostPrice::createFromRequest($request);
    }
}
